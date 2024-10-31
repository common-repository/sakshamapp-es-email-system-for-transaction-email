<?php
 
/*
Plugin Name: Sakshamapp ES email system for transaction email
Plugin URI: https://www.sakshamappinternational.com/sakshamapp-es/
Description: From wordpress we send so many emails in order to ensure the better delivery Sakshamapp ES email system offer a best delivery.
Version: 3.1.7
Author: susheelhbti
Author URI: https://www.sakshamappinternational.com/sakshamapp-es/
License: GPLv2 or later
*/
 
 
 

 
add_action('admin_menu', 'sakshamapp_emailer_create_menu');

function sakshamapp_emailer_create_menu() {

	 
	add_menu_page('Sakshamapp ES', 'Sakshamapp ES', 'manage_options',"sakshamapp_emailer_settings", 'sakshamapp_emailer_options_page' , 'dashicons-email-alt');

	 
    add_submenu_page("sakshamapp_emailer_settings", 'Email log' ,'Email log', 'manage_options', 'sakshamapp_email_log', 'sakshamapp_email_log_page');
	
	
	 
	 add_action( 'admin_init', 'register_sakshamapp_emailer_settings' );
}


 
function sakshamapp_email_log_page() {
    echo "<h2> Email Delivery Log from WordPress using Sakshamapp ES Rest API</h2>";
	echo "<h3>Result showing here only last 100 emails</h3>";
	
	global $wpdb;
	$table_name=SESELOGTBL;
	$emaillogs = $wpdb->get_results( 
	" SELECT * 
	FROM $table_name
	   limit 0,100
	"
);

 
			 
if ( $emaillogs )
{
	
echo "<table border=1><tr><th>ID </th><th>To Name </th><th>To Email </th><th>Subject </th><th>Delivery Time </th><th>Result</th></tr>";

 
foreach($emaillogs as $el)
{
	echo "<tr><td>".$el->id ."</td><td>".$el->to_name ."</td><td>".$el->to_email ."</td><td>".$el->subject ."</td><td>".$el->deliverytime ."</td><td>".$el->status ."</td>";
}

echo "</table>";
 
}
}


function register_sakshamapp_emailer_settings() {
	


	register_setting( 'sakshamapp-emailer-options', 'ses_publicKey' );
	register_setting( 'sakshamapp-emailer-options', 'ses_privateKey' );
 
}

function sakshamapp_emailer_options_page() {
	 
  
?>
<div class="wrap">
<h2>Sakshamapp ES email system for WordPress Transaction Email</h2>
<h2>
<br><br><br>
Step 1: <a href="http://app.sakshamapp.com" target="_blank">Register in the Sakshamapp ES </a>
 http://app.sakshamapp.com
<br><br><br>
Step 2: <a href="http://app.sakshamapp.com/customer/api-keys/index"  target="_blank"> Get API KEY </a>  http://app.sakshamapp.com/customer/api-keys/index
<br><br><br>
Step 3 Enter API key here and start sending 
<br></h2><br><br>
<form method="post" action="options.php">
    <?php settings_fields( 'sakshamapp-emailer-options' ); ?>
    <?php do_settings_sections( 'sakshamapp-emailer-options' ); ?>
	

	
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Public Key</th>
        <td><input type="text" name="ses_publicKey" value="<?php echo esc_attr( get_option('ses_publicKey') ); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Private Key</th>
        <td><input type="text" name="ses_privateKey" value="<?php echo esc_attr( get_option('ses_privateKey') ); ?>" /></td>
        </tr>
         
    </table>
    
    <?php submit_button(); ?>

</form> <?php sakshamappes_sendtestmail() ; ?>
</div>
<?php } 


  function sakshamappes_sendtestmail()
	{
		
		
		
if ($_REQUEST['action']=="")   
	
    {
    ?> <p> Send a test email </p>
			 <form method="post" action="admin.php?page=sakshamapp_emailer_settings">
    	<?php wp_nonce_field('sakshamapp_submit_action'); ?>

		<input type="hidden" name="action" value="submit"> 
         <input type="email" id="testmail" name="testmail" placeholder="email id" value="" required  />

		
		<input type="submit" value="submit"> 
        </form>
		
		 <?php
    } 
else              
	
    {
  $retrieved_nonce = $_REQUEST['_wpnonce'];
if (!wp_verify_nonce($retrieved_nonce, 'sakshamapp_submit_action' ) ) die( 'Failed security check' );

{	

  $testmail=$_REQUEST['testmail']; 
	 
	wp_mail( $testmail, "Test Email by  Sakshamapp ES email system for transaction email","Test Email by  Sakshamapp ES email system for transaction email");
	echo "Test mail sent";
 	}}
 }
	
	
	
	

 
function cURLSakshamappESEmail($to_name,$to_email, $subject, $body_text, $body_html, $from_name, $from_email) {
	
 
 
	$ch = curl_init();
	
 
	curl_setopt($ch, CURLOPT_URL, 'http://app.sakshamapp.com/api_call/v1/transactional_emails.php');
	curl_setopt($ch, CURLOPT_POST, 1);

	 
	$data = 'ses_publicKey='.urlencode(get_option('ses_publicKey')).
			'&ses_privateKey='.urlencode(get_option('ses_privateKey')).
			'&from_email='.urlencode($from_email).
			'&from_name='.urlencode($from_name).
			'&to_email='.urlencode($to_email).
			'&to_name='.urlencode($to_name).
			'&subject='.urlencode($subject);

	if($body_html)	$data .= '&body_html='.urlencode($body_html);
	if($body_text)	$data .= '&body_text='.urlencode($body_text);
	
	


	 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	 
    	$header = "Content-Type: application/x-www-form-urlencoded\r\n";
    	$header .= "Content-Length: ".strlen($data)."\r\n\r\n";

	 
	$result = curl_exec($ch);
	
	 
	curl_close($ch);
	
	
	 global $wpdb;
	 
	$wpdb->insert( 
	 SESELOGTBL,
	array( 
		'from_email' => $from_email,
		'from_name'  => $from_name,
		'to_name'  => $to_name,
		'to_email'  => $to_email,
		'subject'  => $subject,
		'status'  => $result 
	 ), 
	array( 
		'%s', 
		'%s', 
		'%s', 
		'%s', 
		'%s', 
		'%s', 
		) 
	);


	return ($result === false) ? NULL : $result;
	
  
}

 


 if( ! function_exists('wp_mail') ) {
function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
  extract( apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) ) );
  $message = str_replace( "\n", "\r\n", $message );

	if ( !is_array($attachments) )
		$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );

	 
	if ( empty( $headers ) ) {
		$headers = array();
	} else {
		if ( !is_array( $headers ) ) {
			 
			$tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
		} else {
			$tempheaders = $headers;
		}
		$headers = array();
		$cc = array();
		$bcc = array();

		 
		if ( !empty( $tempheaders ) ) {
			 
			foreach ( (array) $tempheaders as $header ) {
				if ( strpos($header, ':') === false ) {
					if ( false !== stripos( $header, 'boundary=' ) ) {
						$parts = preg_split('/boundary=/i', trim( $header ) );
						$boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
					}
					continue;
				}
				 
				list( $name, $content ) = explode( ':', trim( $header ), 2 );

				 
				$name    = trim( $name    );
				$content = trim( $content );

				switch ( strtolower( $name ) ) {
			 
					case 'from':
						if ( strpos($content, '<' ) !== false ) {
						 
							$from_name = substr( $content, 0, strpos( $content, '<' ) - 1 );
							$from_name = str_replace( '"', '', $from_name );
							$from_name = trim( $from_name );

							$from_email = substr( $content, strpos( $content, '<' ) + 1 );
							$from_email = str_replace( '>', '', $from_email );
							$from_email = trim( $from_email );
						} else {
							$from_email = trim( $content );
						}
						break;
					case 'content-type':
						if ( strpos( $content, ';' ) !== false ) {
							list( $type, $charset ) = explode( ';', $content );
							$content_type = trim( $type );
							if ( false !== stripos( $charset, 'charset=' ) ) {
								$charset = trim( str_replace( array( 'charset=', '"' ), '', $charset ) );
							} elseif ( false !== stripos( $charset, 'boundary=' ) ) {
								$boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset ) );
								$charset = '';
							}
						} else {
							$content_type = trim( $content );
						}
						break;
					case 'cc':
						$cc = array_merge( (array) $cc, explode( ',', $content ) );
						break;
					case 'bcc':
						$bcc = array_merge( (array) $bcc, explode( ',', $content ) );
						break;
					default:
					 
						$headers[trim( $name )] = trim( $content );
						break;
				}
			}
		}
	}

 
	if ( !isset( $from_name ) )
		$from_name = 'WordPress';
 

	if ( !isset( $from_email ) ) {
	 
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}

		$from_email = 'wordpress@' . $sitename;
	}

	 
	$from_email		= apply_filters( 'wp_mail_from'     , $from_email );
	$from_name		= apply_filters( 'wp_mail_from_name', $from_name  );

 
	if ( !isset( $content_type ) )
		$content_type = 'text/plain';

	$content_type = apply_filters( 'wp_mail_content_type', $content_type );

  
  if (  is_array( $to ) )
	{
		
		 

		 
  foreach($to as $v)
  {
  cURLSakshamappESEmail($v,$v,  $subject, $message, $message, $from_name, $from_email) ;
  }
 }
	else
	{
		cURLSakshamappESEmail($to,$to,  $subject, $message, $message, $from_name, $from_email) ;
	 
	}
	
 
}
}




function SakshamappES_install () {
   global $wpdb;
 
	global $sakshamappES_db_version;

	$table_name =SESELOGTBL;
	
	$charset_collate = $wpdb->get_charset_collate();

	 
	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		from_email varchar(100),
		from_name varchar(100),
		to_name varchar(100),
		to_email varchar(100),
		subject varchar(100),
		status varchar(100),
		deliverytime datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		UNIQUE KEY id (id)
	) $charset_collate;";

	
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	dbDelta( $sql );
	add_option( 'sakshamappES_db_version', $sakshamappES_db_version );
	
	
}

define("SESELOGTBL",$wpdb->prefix . 'sakshamapp_email_log');

add_option( "sakshamappES_db_version", "1.0" );
register_activation_hook( __FILE__, 'SakshamappES_install' );

