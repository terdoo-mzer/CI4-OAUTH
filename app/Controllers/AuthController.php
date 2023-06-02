<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\App;

class AuthController extends BaseController
{

    // private $config = new App();
    // private $baseURL = $config->baseURL;
    public function index()
    {
        $data = [];
        $data['name'] = "Terdoo";
        return view('login', $data);
    }

    public function auth()
    {

        $session = session();

        $config = new App();
        $baseURL = $config->baseURL;

        $clientID = 'wprQYMZBqqx-dgszFUfQG';
        $tokenURL = 'https://id-sandbox.cashtoken.africa/oauth/token';
        $authURL  = 'https://id-sandbox.cashtoken.africa/oauth/authorize';


        // Authorixation Request
        // This will riderect the user to the authorization page or prompt.
        // When they grant permission, they wil be redirected back to the app via the redirect uri
        // with a code and state like below
        // http://localhost:3000/callback?
        // code=nV8MGd_LJmUg2vedRv8hmggFWHsPGyOxehz9wzQz0GD
        // &state=9cb742bd29fd89c7bd25f839edfc13ba
        if (!isset($_GET['action'])) {

            // $_SESSION['state']    = bin2hex(random_bytes(16));
            $session->set('state', bin2hex(random_bytes(16)));
            $verifier_bytes = random_bytes(64);
            $code_verifier = rtrim(strtr(base64_encode($verifier_bytes), "+/", "-_"), "=");
            $session->set('verifier', $code_verifier);
            // $_SESSION['verifier'] = $code_verifier;
            $challenge_bytes = hash("sha256", $code_verifier, true);
            $code_challenge = rtrim(strtr(base64_encode($challenge_bytes), "+/", "-_"), "=");


            $params = array(
                'response_type'  => 'code',
                'client_id'      => $clientID,
                'code_challenge' => $code_challenge,
                'code_challenge_method' => 'S256',
                'redirect_uri'   => $baseURL . "callback",
                'scope'          => 'openid email profile',
                'state'          => $_SESSION['state']
                // 'state'          => $session->has('state')
            );

            // echo "Gotcha now!";
            // die();

            return redirect()->to($authURL . '?' . http_build_query($params));
            // header('Location: ' . $authURL . '?' . http_build_query($params));
            die();
        }
    }

    public function callBack()
    {
        $session = session();

        $clientID = 'wprQYMZBqqx-dgszFUfQG';
        $tokenURL = 'https://id-sandbox.cashtoken.africa/oauth/token';
        $authURL  = 'https://id-sandbox.cashtoken.africa/oauth/authorize';

        $config = new App();
        $baseURL = $config->baseURL;



        // At this point, the code and state should be ready from the previous request
        if (isset($_GET['code'])) {

            // print_r($_GET['code']);

            // If state is not set then redirect user
            /**
             * Note:
             * The conditional statemnt below will check and compare the state that was generated
             * above to the one that is returned from the google authorization request.
             * They both have to match to proceed.
             * 
             */
            if (!isset($_GET['state']) || ($_SESSION['state'] != $_GET['state'])) {
                header('location: ' . $baseURL . '?error=state_not_found');
                // header('location: '.$baseUrl);
                die();
            }

            $ch = curl_init($tokenURL);
            $fields = [
                'grant_type' => "authorization_code",
                'client_id'  => $clientID,
                'code_verifier' => $_SESSION['verifier'],
                'redirect_uri'  => $baseURL . 'callback',
                'code'          => $_GET['code']

            ];

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));

            $rx = curl_exec($ch);
            $data = json_decode($rx, true);
            curl_close($ch);
            $jwt = explode('.', $data['id_token']);
            $res = json_decode(base64_decode(($jwt[1])), true);

            $_SESSION['user_data'] = json_encode([
                'user_id'      => $res['sub'],
                'email'        => $res['email'],
                'type'         => $res['type'],
                'display_name' => $res['display_name'],
                'first_name'   => $res['first_name'],
                'last_name'    => $res['last_name'],
                'username'     => $res['username']
            ]);

            $_SESSION['access_token'] = $data['access_token'];
            $_SESSION['id_token'] = $data['id_token'];

            // echo "gotcha!";
            // print_r($res);
            // die();

            // header('Location: ' . $baseURL . 'dashboard');
            // header('Location: http://localhost:3000/dashboard');
            return redirect()->to('dashboard');
            die();
        }
    }

    public function profile()
    {
        return view('dashboard');
    }

    function logout()
    {
        $config = new App();
        $baseURL = $config->baseURL;
        $session = session();

        if (isset($_GET['logout'])) {
            $session->destroy();
            unset($_SESSION);
            header('Location: ' . $baseURL);
            die();
        }
    }
}
