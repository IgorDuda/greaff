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
    private $github;
    private $session;

    public function onDispatch(MvcEvent $e)
    {
        return parent::onDispatch($e);
    }

    public function __construct()
    {
        $reader = new \Zend\Config\Reader\Ini();
        $data = $reader->fromFile(ROOT_PATH.'/config/github.ini');
        $this->github = new Github($data['clientId'], $data['clientSecret']);
        $this->session = new Container('github');
    }

    public function indexAction()
    {
        if ($this->isTokenExists()) {
            $this->redirect()->toRoute('application', array('action' => 'user'));
        } else {
            $this->redirect()->toUrl($this->github->authorize());
        }
    }

    public function callbackAction()
    {
        try {
            $request = $this->params()->fromQuery();
            
            $this->saveToken(
                    $this->github
                        ->setCode($request['code'])
                        ->createAccessToken()
                );

            $this->redirect()->toRoute('application', array('action' => 'user'));
        } catch (\Exception $x) {
            echo '<pre>';
            printf('%s:%s\n\n%s', get_class($x), $x->getMessage(), $x->getTraceAsString());
            die;
        }
    }

    public function userAction()
    {
        $user = $this->github->me($this->getToken());
        return new ViewModel(array('user' => $user));
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

    private function saveToken($accessToken)
    {
        $this->session->offsetSet('access_token', $accessToken);
    }

    private function getToken()
    {
        return $this->session->offsetGet('access_token');
    }

    private function isTokenExists()
    {
        return $this->session->offsetExists('access_token') ? true : false;
    }

    private function unsetToken()
    {
        $this->session->offsetUnset('access_token');
    }
}
