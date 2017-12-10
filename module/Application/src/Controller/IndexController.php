<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Application\Models\Github\Github;
use Application\Form\SearchForm;

class IndexController extends AbstractActionController
{
    /**
     * @var Application\Models\Github\Github
     */
    private $github;
    
    /**
     * @var Zend\Session\Container 
     */
    private $session;
    
    const SESSION_KEY = 'access_token';
    const GITHUB_INI = 'github.ini';

    public function __construct()
    {
        $this->session = new Container('github');
    }

    /**
     * Execute the request
     *
     * @param  MvcEvent $e
     * @return mixed
     * @throws Exception\DomainException
     */
    public function onDispatch(MvcEvent $e)
    {
        if(!$this->isTokenExists()) {
            $this->redirect()->toUrl($this->github->authorize());
        }
        
        $reader = new \Zend\Config\Reader\Ini();
        $data = $reader->fromFile(CONFIG_PATH . self::GITHUB_INI);
        $this->github = new Github($data['clientId'], $data['clientSecret']);
        return parent::onDispatch($e);
    }

    /**
     * Displays github information about authorized user
     * 
     * @return ViewModel
     */
    public function indexAction()
    {
        $user = $this->github->me($this->getToken());
        return new ViewModel(array('user' => $user));
    }

    /**
     * Authorization callback URL
     */
    public function callbackAction()
    {
        try {
            $request = $this->params()->fromQuery();

            $this->saveToken(
                $this->github
                    ->setCode($request['code'])
                    ->createAccessToken()
            );

            $this->redirect()->toRoute('application', array('action' => 'index'));
        } catch (\Exception $x) {
            echo '<pre>';
            printf('%s:%s\n\n%s', get_class($x), $x->getMessage(), $x->getTraceAsString());
            die;
        }
    }

    /**
     * Displays list of github users according to the searching query
     * 
     * @todo Implement pagination
     * @return ViewModel
     */
    public function searchAction()
    {
        $form = new SearchForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = $request->getPost();
            $form->setData($data);
            $query = $data['query'];

            return new ViewModel(array(
                'users' => $this->github->searchUser($query),
                'query' => $query,
                'form' => $form
            ));
        }

        return new ViewModel(array(
            'form' => $form
        ));
    }

    /**
     * Set access token into session
     * 
     * @param string $accessToken
     */
    private function saveToken($accessToken)
    {
        $this->session->offsetSet(self::SESSION_KEY, $accessToken);
    }

    /**
     * Get access token from session
     * 
     * @param string $accessToken
     */
    private function getToken()
    {
        return $this->session->offsetGet(self::SESSION_KEY);
    }

    /**
     * Check if access token exists
     * 
     * @return boolean
     */
    private function isTokenExists()
    {
        return $this->session->offsetExists(self::SESSION_KEY) ? true : false;
    }

    /**
     * Delete access token from sesion
     */
    private function unsetToken()
    {
        $this->session->offsetUnset(self::SESSION_KEY);
    }
}
