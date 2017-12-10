<?php

namespace Application\Models\Github;

class Urls
{
    const authorize = "https://github.com/login/oauth/authorize";
    const access_token = "https://github.com/login/oauth/access_token";
    const current_user = "https://api.github.com/user";
    const current_user_authorizations_html = "https://github.com/settings/connections/applications{/client_id}";
    const authorizations = "https://api.github.com/authorizations";
    const code_search = "https://api.github.com/search/code?q={query}{&page,per_page,sort,order}";
    const commit_search = "https://api.github.com/search/commits?q={query}{&page,per_page,sort,order}";
    const emails = "https://api.github.com/user/emails";
    const emojis = "https://api.github.com/emojis";
    const events = "https://api.github.com/events";
    const feeds = "https://api.github.com/feeds";
    const followers = "https://api.github.com/user/followers";
    const following = "https://api.github.com/user/following{/target}";
    const gists = "https://api.github.com/gists{/gist_id}";
    const hub = "https://api.github.com/hub";
    const issue_search = "https://api.github.com/search/issues?q={query}{&page,per_page,sort,order}";
    const issues = "https://api.github.com/issues";
    const keys = "https://api.github.com/user/keys";
    const notifications = "https://api.github.com/notifications";
    const organization_repositories = "https://api.github.com/orgs/{org}/repos{?type,page,per_page,sort}";
    const organization = "https://api.github.com/orgs/{org}";
    const public_gists = "https://api.github.com/gists/public";
    const rate_limit = "https://api.github.com/rate_limit";
    const repository = "https://api.github.com/repos/{owner}/{repo}";
    const repository_search = "https://api.github.com/search/repositories?q={query}{&page,per_page,sort,order}";
    const current_user_repositories = "https://api.github.com/user/repos{?type,page,per_page,sort}";
    const starred = "https://api.github.com/user/starred{/owner}{/repo}";
    const starred_gists = "https://api.github.com/gists/starred";
    const team = "https://api.github.com/teams";
    const user = "https://api.github.com/users/{user}";
    const user_organizations = "https://api.github.com/user/orgs";
    const user_repositories = "https://api.github.com/users/{user}/repos{?type,page,per_page,sort}";
    const user_search = "https://api.github.com/search/users";
}
