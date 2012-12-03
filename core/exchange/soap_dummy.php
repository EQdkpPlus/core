<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:	     	http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2009 sz3
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */
 
if (!defined('EQDKP_INC'))
{
  die('Do not access this file directly.');
}

if (!class_exists('exchange_soap_dummy')){
class exchange_soap_dummy{
       
        var $options = array();
        var $type = 'SOAP';
       
        function __construct(){
                $this->options = array(
                        'input' => array(
                                        'username'      => 'string',
                                        'password'      => 'string',
                                ),
                        'output' => array(
                                        'Return'        => 'string',
                                ),
                );
        }
       
       
        function soap_dummy($username, $password){
					global $oauth, $core;
            //For Authentification, please use oauth!
						//Never send credentials through SOAP!!
						
						try {
							$req = OAuthRequest::from_request();
							$params = $req->get_parameters();
							return $params['oauth_consumer_key'];
							
							if (!$oauth->app_request($params['oauth_consumer_key'])){
								return 'oauth_token=&oauth_token_secret=';
							} else {
								$token = $oauth->fetch_access_token($req);
								return $token;
							}
				
						} catch (OAuthException $e) {
							if ($core->config['pk_debug'] == 2 || $core->config['pk_debug'] == 3){
								print($e->getMessage() . "\n<hr />\n");
								print_r($req);
							} else {
								return 'oauth_token=&oauth_token_secret=';
							}	
						
						}
						  
            return 'Username: '.$username.'Password: '.$password;           
        }
}
}
?>
