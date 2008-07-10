<?php
   /**
    * Clansuite - just an eSports CMS
    * Jens-Andre Koch � 2005 - onwards
    * http://www.clansuite.com/
    *
    * LICENSE:
    *
    *    This program is free software; you can redistribute it and/or modify
    *    it under the terms of the GNU General Public License as published by
    *    the Free Software Foundation; either version 2 of the License, or
    *    (at your option) any later version.
    *
    *    This program is distributed in the hope that it will be useful,
    *    but WITHOUT ANY WARRANTY; without even the implied warranty of
    *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    *    GNU General Public License for more details.
    *
    *    You should have received a copy of the GNU General Public License
    *    along with this program; if not, write to the Free Software
    *    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    *
    * @license    GNU/GPL, see COPYING.txt
    *
    * @author     Jens-Andre Koch <vain@clansuite.com>
    * @copyright  Copyleft: All rights reserved. Jens-Andre Koch (2005 - onwards)
    *
    * @link       http://www.clansuite.com
    * @link       http://gna.org/projects/clansuite
    * @since      File available since Release 0.2
    *
    * @version    SVN: $Id$
    */

//Security Handler
if (!defined('IN_CS')){ die('Clansuite not loaded. Direct Access forbidden.' );}


/**
 * Clansuite
 *
 * Module:  Account (User Account Registration/ Login / Logout etc. )
 *
 */
class Module_Account extends ModuleController implements Clansuite_Module_Interface
{
    /**
     * Module_Admin -> Execute
     */
    public function execute(httprequest $request, httpresponse $response)
    {
        # proceed to the requested action
        $this->processActionController($request);
        # read module config
        #$this->config->readConfig( ROOT_MOD . '/admin/admin.config.php');
    }

    public function action_show()
    {
        $this->setTemplate('login.tpl');
        $this->prepareOutput();
    }

    /**
     * Login Block
     */
    public function login()
    {
        # Get Render Engine
        $smarty = $this->getView();

        // get user class
        $user = $this->injector->instantiate('Clansuite_User');
        $config = $this->injector->instantiate('Clansuite_Config');
        
        // Login Form / User Center
        if ( $_SESSION['user']['user_id'] == 0 )
        {
            // Assing vars & output template

            // Assing vars & output template
            $smarty->assign('config', $config);
            $smarty->assign('error', $error);

            $this->setTemplate('login.tpl');
            $this->prepareOutput();
        }
        else
        {
            //  Show usercenter
            $this->setTemplate('usercenter.tpl');

            $this->prepareOutput();

        }
        
    }

    /**
     * Login
     */
    public function action_login()
    {
        // Set Pagetitle and Breadcrumbs
        trail::addStep( _('Login'), '/index.php?mod=account&amp;action=login');

        // Get Inputvariables
        $request = $this->injector->instantiate('httprequest');
        # from $_POST
        $nick        = $request->getParameter('nickname');
        $email       = $request->getParameter('email');
        $password    = $request->getParameter('password');
        $remember_me = $request->getParameter('remember_me');
        $submit      = $request->getParameter('submit');
        # from $_GET
        $referer	 = $request->getParameter('referer');

        // Set Error Array
        $error = array();

        $config = $this->injector->instantiate('Clansuite_Config');

        // Determine the Login method
        if( $config['login']['login_method'] == 'nick' )
        {
            $value = $nick;
        }
        elseif( $config['login']['login_method'] == 'email' )
        {
            $value = $email;
        }

        // get user class
        $user = $this->injector->instantiate('Clansuite_User');

        // Perform checks on Inputvariables & Form filled?
        if ( isset($value) && !empty($value) && !empty($password) )
        {
            // ban ip
            if ( !empty($_SESSION['login_attempts'])
                 AND $_SESSION['login_attempts'] >= $config['login']['max_login_attempts'] )
            {
                # @todo: ban action
                $this->redirect('index.php', 3, '200', _('You are temporarily banned for the following amount of minutes:').'<br /><b>'.$config['login']['login_ban_minutes'].'</b>' );
            }

            // check whether user_id + password match
            $user_id = $user->checkUser($config['login']['login_method'], $value, $password);

            // proceed if true
            if ($user_id != false)
            {
                // perform login for user_id and redirect

                $user->loginUser( $user_id, $remember_me, $password );

                #$this->redirect( !empty($referer) ? WWW_ROOT . '/' . base64_decode($referer) : 'index.php', 'metatag|newsite', 3 , _('You successfully logged in...') );
            }
            else
            {
                // log the login attempts to ban the ip at a specific number
                if (!isset($_SESSION['login_attempts']))
                {
                    $_SESSION['login_attempts'] = 1;
                }
                else
                {
                    # @todo: whats LOGIN_ALREADY??
                    #if( !defined('LOGIN_ALREADY') )
                    #{
                        #define('LOGIN_ALREADY', 1);
                        $_SESSION['login_attempts']++;
                    #}
                }

                // Error Variables
                $error['mismatch'] = 1;
                $error['login_attempts'] = $_SESSION['login_attempts'];
            }
        }
        else
        {
            if ( isset ( $submit ) )
            { $error['not_filled'] = 1; }
        }

        # Get Render Engine
        $smarty = $this->getView();

        // Login Form / User Center
        if ( $_SESSION['user']['user_id'] == 0 )
        {
            // Assing vars & output template
            $smarty->assign('config', $config);
            $smarty->assign('error', $error);
            $smarty->assign('referer', $referer);
            //return $smarty->fetch('login.tpl');
        }
        else
        {
            //  Show usercenter
            #var_dump($smarty);
            $this->setTemplate('usercenter.tpl');
        }

        # Prepare the Output
        $this->prepareOutput();
    }

    /**
     * @desc Logout
     *
     * @input: $confirm
     *
     * If logout is confirmed:
     *
     * Destroy Session
     * Delete Cookie
     * Redirect to index.php
     *
     * else:
     * @output: $tpl->fetch( 'account/logout.tpl' )
     *
     */
    public function action_logout()
    {
        // Set Pagetitle and Breadcrumbs
        trail::addStep( _('Logout'), '/index.php?mod=account&amp;action=logout');

        // Get Inputvariables
        $request = $this->injector->instantiate('httprequest');

        // $_POST
        $confirm = (int) $request->getParameter('confirm');

        // User instance
        $user = $this->injector->instantiate('Clansuite_User');


        if( $confirm == 1 )
        {
            // Logout the user
            $user->logoutUser();

            // Redirect
            $this->redirect( 'index.php', 3, 200, _( 'You have successfully logged out...') );
        }
        else
        {
            # Prepare the Output
            $this->prepareOutput();
        }
    }

    /**
    * @desc Register a User
    *
    * Get $_POST INPUT
    * @Input: email, email2, nick, password, password2, submit, captcha
    *
    * Perform checks on $_POST
    * Generate Activation Code
    * Insert User into DB -> VALUES (:email, :nick, :password, :joined, :code)');
    * Get user id
    * Load Mail & Send mail to User
    * Assign Captcha to Template
    * Show template
    *
    * @Output :  $tpl->fetch('account/register.tpl');
    *
    */

    public function action_register()
    {
        // Request Controller
        $request = $this->injector->instantiate('httprequest');
        $input = $this->injector->instantiate('input');
        $security = $this->injector->instantiate('Clansuite_Security');
        $config = $this->injector->instantiate('Clansuite_Config');
        $smarty = $this->getView();
        
        // Get Inputvariables from $_POST
        $email      = $request->getParameter('email');
        $email2     = $request->getParameter('email2');
        $nick       = $request->getParameter('nick');
        $pass       = $request->getParameter('password');
        $pass2      = $request->getParameter('password2');
        $submit     = $request->getParameter('submit');
        $captcha    = $request->getParameter('captcha');

        // Set Error Array
        $err = array();

        // Perform checks on Inputvariables & Form filled?
        if ( empty($email) OR empty($email2) OR empty($nick) OR empty($pass) OR empty($pass2) )
        {
            if( isset($submit) )
            {
                // Not all necessary fields are filled
                $err['not_filled'] = 1;
            }
        }
        else
        {   // Form is filled

            // Check both emails match
            if ($email != $email2 )
            {
                $err['emails_mismatching'] = 1;
            }

            // Check email
            if ($input->check($email, 'is_email' ) == false )
            {
                $err['email_wrong'] = 1;
            }

            // Check nick
            if ($input->check($nick, 'is_abc|is_int|is_custom', '-_()<>[]|.:\'{}$', 25 ) == false )
            {
                $err['nick_wrong'] = 1;
            }

            // Check both passwords
            if ($pass != $pass2 )
            {
                $err['passes_do_not_fit'] = 1;
            }

            // Check for correct Captcha
            if (strtolower($captcha) != strtolower($_SESSION['captcha_string']) )
            {
                $err['wrong_captcha'] = 1;
            }

            // Check the password
            if (strlen($pass) < $config['login']['min_pass_length'])
            {
                $err['pass_too_short'] = 1;
            }

            // Check if email already exists
            $result = Doctrine_Query::create()
                            ->select('email')
                            ->from('CsUsers')
                            ->where('email = ?')
                            ->fetchOne(array($email), Doctrine::FETCH_ARRAY);
                         
            if( $result )
                $err['email_exists'] = 1;
            
            // Check if nick already exists
            $result = Doctrine_Query::create()
                            ->select('nick')
                            ->from('CsUsers')
                            ->where('nick = ?')
                            ->fetchOne(array($nick), Doctrine::FETCH_ARRAY);
                         
            if( $result )
                $err['nick_exists'] = 1;

            #var_dump($err);    
            // No errors - then proceed
            // Register the user!
            if ( count($err) == 0  )
            {
                // Generate activation code & salted hash

                $code = md5 ( microtime() );
                $hashArr = $security->build_salted_hash();
                $hash = $hashArr['hash'];
                $salt = $hashArr['salt'];
                
                $userIns = new CsUsers();
                $userIns->activation_code = $code;
                $userIns->email = $email;
                $userIns->nick = $nick;
                $userIns->passwordhash = $hash;
                $userIns->salt = $salt;
                $userIns->joined = time();
                $userIns->save();
                
                // Send activation mail                
                if( $this->_send_activation_email($email, $nick, $userIns->user_id, $code) )
                {
                    $this->redirect( 'index.php', 200, _('You have sucessfully registered! Please check your mailbox...') );   
                }
                else
                {
                    trigger_error( 'Sending of email activation failed.' );
                }
            }
        }

        // Assign vars
        $smarty->assign( 'min_length', $config['login']['min_pass_length'] );
        $smarty->assign( 'err', $err );
        #$smarty->assign( 'captcha_url',  WWW_ROOT . '/index.php?mod=captcha&' . session_name() . '=' . session_id() );

        // Get the template
        $this->setTemplate('register.tpl');
        
        // Output
        $this->prepareOutput();
    }

    /**
    * @desc Re-Send Activation Email
    */
    public function action_activation_email()
    {
        $err = array();
        
        // Request Controller
        $request = $this->injector->instantiate('httprequest');        
        
        // Input validation
        $input = $this->injector->instantiate('input');
        
        // Get Inputvariables from $_POST
        $email  = $request->getParameter('email');
        $submit = $request->getParameter('submit');

        // Perform checks on Inputvariables & Form filled?
        if ( empty($email) )
        {
            if ( !empty ( $submit ) )
            {
                $err['form_not_filled'] = 1;
            }
        }
        else
        {   // Form filled -> proceed

            if ( !$input->check( $email, 'is_email' ) )
            {
                $err['email_wrong'] = 1;
            }

            // No Input-Errors
            if ( count($err) == 0 )
            {
                // Select WHERE email
                $result = Doctrine_Query::create()
                                ->select('user_id,nick,activated')
                                ->from('CsUsers')
                                ->where('email = ?')
                                ->fetchOne(array($email));
                
                // Email was not found
                if ( !$result )
                {
                    $err['no_such_mail'] = 1;
                }
                else
                {
                    // Email already activated
                    if ( $result->activated == 1 )
                    {
                        $err['already_activated'];
                    }

                    // Email was found & is not active
                    if ( count($err) == 0 )
                    {
                        // Prepare user_id, nick, and activation code
                        $code    = md5 ( microtime() );

                        // Insert Code into DB WHERE user_id
                        $result->activation_code = $code;
                        $result->save();

                        if( $this->_send_activation_email($email, $result->nick, $result->user_id, $code) )
                        {
                            $this->redirect( 'index.php', 200, _('Activation mail has been resend to your mailbox.') );   
                        }
                        else
                        {
                            trigger_error( 'Re-Sending of email activation failed.' );
                        }
                    }
                }
            }
        }

        // get View Ctrl.
        $smarty = $this->getView();
        
        // Assign tpl vars
        $smarty->assign( 'err', $err );

        // Output
        $this->setTemplate('activation_email.tpl');
        $this->prepareOutput();
    }

    /**
    * @desc Activate Account
    *
    * @input: user_id, code
    *
    * validate code
    * SELECT activated WHERE user_id and code
    * 1. code wrong for user_id
    * 2. code found, but already activated=1
    * 3. code found, SET activated=1
    *
    * @output:
    *
    */

    public function action_activate_account()
    {
        // Request Controller
        $request = $this->injector->instantiate('httprequest');        

        // Get Inputvariables from $_GET
        $user_id = (int) $request->getParameter('user_id');
        $code    = $input->check($request->getParameter('code'), 'is_int|is_abc') ? $request->getParameter('code') : false;

        // Activation code is wrong
        if ( !$code )
        {
            $this->output .= $error->show( _( 'Code Failure' ), _('The given activation code is wrong. Please make sure you copied the whole activation URL into your browser.'), 2 );
            return;
        }

        // SELECT activated WHERE user_id and code
        $stmt = $db->prepare( 'SELECT activated FROM ' . DB_PREFIX . 'users WHERE user_id = ? AND code = ?' );
        $stmt->execute( array( $user_id, $code ) );
        $res = $stmt->fetch();

        if ( is_array ( $res ) )
        {
            // Account already activated
            if ( $res['activated'] == 1 )
            {
                $this->output .= $error->show( _( 'Already' ), _('This account has been already activated.'), 2 );
                return;
            }
            else
            {
                // UPDATE activated=1 WHERE user_id
                $stmt = $db->prepare( 'UPDATE ' . DB_PREFIX . 'users SET activated = ? WHERE user_id = ?' );
                $stmt->execute( array ( 1, $user_id ) );
                $this->redirect( 'index.php?mod=account&action=login', 'metatag|newsite', 3, _('Your account has been activated successfully - please login.') );
            }
        }
        else
        {   // Activation Code not matching user_id
            $this->output .= $error->show( _( 'Code Failure' ), _('The activation code does not match to the given user id'), 2 );
            return;
        }
    }

    /**
    * @desc Forgot Password
    */

    public function action_forgot_password()
    {
        // Request Controller
        $request = $this->injector->instantiate('httprequest');        
        $input = $this->injector->instantiate('input');
        
        $email = $request->getParameter('email');

        if( empty($email) )
        {
            $err['form_not_filled'] = 1;
        }
        else
        {
            if ( !$input->check( $email, 'is_email' ) )
            {
                $err['email_wrong'] = 1;
            }

            if ( count($err) == 0 )
            {
                
                $stmt = $db->prepare( 'SELECT user_id,nick FROM ' . DB_PREFIX . 'users WHERE email = ?' );
                $stmt->execute( array($email) );
                $res = $stmt->fetch();

                if ( !is_array($res) )
                {
                    $err['no_such_mail'] = 1;
                }
                else
                {
                    if ( count($err) == 0 )
                    {
                        $random   = $functions->random_string(7);
                        $user_id  = $res['user_id'];
                        $nick     = $res['nick'];
                        $code     = md5 ( microtime() );
                        $new_pass = $security->db_salted_hash($random);

                        $stmt = $db->prepare( 'UPDATE ' . DB_PREFIX . 'users SET activation_code = ?, new_password = ? WHERE user_id = ?' );
                        $stmt->execute( array ( $code, $new_pass, $user_id ) );

                        // Load mailer
                        require ( ROOT_CORE . '/mail.class.php' );
                        $mailer = new mailer;

                        $to_address     = '"' . $nick . '" <' . $email . '>';
                        $from_address   = '"' . $config->fromname . '" <' . $config->from . '>';
                        $subject        = _('Password reset');

                        $body  = _("Your password would be resetted by clicking on this link:\r\n");
                        $body .= WWW_ROOT."/index.php?mod=account&action=activate_password&user_id=%s&code=%s\r\n";
                        $body .= "----------------------------------------------------------------------------------------------------------\r\n";
                        $body .= _('Username').": %s\r\n";
                        $body .= _('New Password').": %s\r\n";
                        $body .= "----------------------------------------------------------------------------------------------------------\r\n";
                        $body  = sprintf($body, $user_id, $code, $nick, $random);

                        // Send mail
                        if ( $mailer->sendmail($to_address, $from_address, $subject, $body) == true )
                        {
                            $this->redirect( 'index.php', 'metatag|newsite', 3, _('You have sucessfully received the password activation mail! Please check your mailbox...') );
                        }
                        else
                        {
                            $this->output .= $error->show( _( 'Mailer Error' ), _( 'There has been an error in the mailing system. Please inform the webmaster.' ), 2 );
                            return;
                        }
                    }
                }
            }
        }

        // Assign tpl vars
        $tpl->assign( 'err', $err );

        // Output
        $this->output .= $tpl->fetch('account/forgot_password.tpl');
    }

    /**
    * @desc Activate Password
    */

    public function action_activate_password()
    {
        // Request Controller
        $request = $this->injector->instantiate('httprequest');
        $input = $this->injector->instantiate('input');
        
        $user_id = (int) $request->getParameter('user_id');
        $code    = $input->check($request->getParameter('code'), 'is_int|is_abc') ? $request->getParameter('code') : false;

        if ( !$code )
        {
            $this->output .= $error->show( _( 'Code Failure' ), _('The given activation code is wrong. Please make sure you copied the whole activation URL into your browser.'), 2 );
            return;
        }

        $stmt = $db->prepare( 'SELECT user_id,activated,new_password FROM ' . DB_PREFIX . 'users WHERE user_id = ? AND code = ?' );
        $stmt->execute( array( $user_id, $code ) );
        $res = $stmt->fetch();
        if ( is_array ( $res ) )
        {
            if ( empty($res['new_password']) )
            {
                $this->output .= $error->show( _( 'Already' ), _('There has been no password reset request.'), 2 );
                return;
            }
            else
            {
                $stmt = $db->prepare( 'UPDATE ' . DB_PREFIX . 'users SET password = new_password WHERE user_id = ?' );
                $stmt->execute( array ( $user_id ) );

                setcookie('user_id', false);
                setcookie('password', false);

                $this->redirect( 'index.php?mod=account&action=login', 'metatag|newsite', 3, _('Your new password has been successfully activated. Please login...') );
            }
        }
        else
        {
            $this->output .= $error->show( _( 'Code Failure' ), _('The activation code does not match to the given user id'), 2 );
            return;
        }
    }
    
    /**
    * @desc Private Function to send a activation email
    */
    private function _send_activation_email($email, $nick, $user_id, $code)
    {
        $config = $this->injector->instantiate('Clansuite_Config');
        $mailer = new Clansuite_Mailer;
        
        $to_address     = '"' . $nick . '" <' . $email . '>';
        $from_address   = '"' . $config['email']['fromname'] . '" <' . $config['email']['from'] . '>';
        $subject        = _('Account activation');

        $body  = _("To activate your account click on the link below:\r\n");
        $body .= WWW_ROOT."/index.php?mod=account&action=activate_account&user_id=%s&code=%s\r\n";
        $body .= "----------------------------------------------------------------------------------------------------------\r\n";
        $body .= _('Username').": %s\r\n";
        $body .= _('Password').": *"._('hidden')."*";
        $body .= "----------------------------------------------------------------------------------------------------------\r\n";
        $body  = sprintf($body, $user_id, $code, $nick);

        // Send mail
        if ( $mailer->sendmail($to_address, $from_address, $subject, $body) == true )
        {
            return true;
        }
        else
        {
            trigger_error( _( 'Mailer Error: There has been an error in the mailing system. Please inform the webmaster.' ) );
            return false;
        }
    }
}