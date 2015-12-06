<?php
namespace CrudKit\Pages;

use CrudKit\Util\FlashBag;
use CrudKit\Util\RouteGenerator;
use CrudKit\Util\TwigUtil;
use CrudKit\Util\UrlHelper;
use CrudKit\Util\ValueBag;

class BasicLoginPage extends BasePage {
	protected $url;
	public function __construct () {
		$this->setId ("__ck_basic_login");
		$this->url = new UrlHelper ();
		$this->route = new RouteGenerator();
	}

	public function userTriedLogin () {
		return $this->url->has ('username') && $this->url->has ('password');
	}

	public function getUserName () {
		return $this->url->get ('username');
	}

	public function getPassword () {
		return $this->url->get ('password');
	}

	// TODO: Refactor this into a session helper
	public function check () {
		return isset($_SESSION['__ck_logged_in']) && $_SESSION['__ck_logged_in'] == true;
	}

	public function doLogin ($username = null) {
		$_SESSION['__ck_logged_in'] = true;
		$_SESSION['__ck_username'] = $username;
	}

	protected $loginQueued = false;

	public function queueLogin () {
		$this->loginQueued = true;
	}

	public function success () {
		// Don't do the login writing to the session just yet
		// since we might be on a different page id than expected,
		// we need to do a clean redirect
		$this->queueLogin ();
	}

	protected $error = null;
	public function fail ($error) {
		$this->error = $error;
	}

    function render()
    {
    	if ($this->loginQueued) {
    		FlashBag::add ('success', "Login Success");
    		$this->doLogin ();
    		return array (
    			'type' => 'redirect',
    			'url' => $this->route->root ()
			);
    	}

        return array (
        	'type' => 'template',
        	'template' => 'pages/login.twig',
        	'data' => [
	        	'staticRoot' => $this->app->getStaticRoot(),
	        	'title' => $this->app->getAppName (),
	        	'endpoint' => $this->route->root (),
	        	'valueBag' => json_encode(ValueBag::getValues()),
	        	'bodyclass' => 'login-page',
	        	'error' => $this->error,
	        	'username' => $this->url->get ('username', '')
        	]
    	);
    }
}