<?php
/**
 * @package MPesa For WooCommerce
 * @subpackage C2B Library
 * @author Osen Concepts < hi@osen.co.ke >
 * @version 1.10
 * @since 0.18.01
 */

/**
 * 
 */
class MpesaC2BWP
{
  /**
   * @param string  | Environment in use    | live/sandbox
   */
  public static $env = 'sandbox';

  /** 
   * @param string | Daraja App Consumer Key   | lipia/validate
   */
  public static $appkey;

  /** 
   * @param string | Daraja App Consumer Secret   | lipia/validate
   */
  public static $appsecret;

  /** 
   * @param string | Online Passkey | lipia/validate
   */
  public static $passkey;

  /** 
   * @param number  | Head Office Shortcode | 123456
   */
  public static $headoffice;

  /** 
   * @param number  | Business Paybill/Till | 123456
   */
  public static $shortcode;

  /** 
   * @param integer | Identifier Type   | 1(MSISDN)/2(Till)/4(Paybill)
   */
  public static $type = 4;

  /** 
   * @param string | Validation URI   | lipia/validate
   */
  public static $validate;

  /** 
   * @param string  | Confirmation URI  | lipia/confirm
   */
  public static $confirm;

  /** 
   * @param string  | Reconciliation URI  | lipia/reconcile
   */
  public static $reconcile;

  /** 
   * @param string  | Timeout URI   | lipia/reconcile
   */
  public static $timeout;

  function __construct()
  {
    /**
     * Setup CORS 
     */
    header( 'Access-Control-Allow-Origin: *' );
  }

  /**  
   * @param Array $config - Key-value pairs of settings
   */
  public static function set( $config )
  {
    foreach ( $config as $key => $value ) {
      self::$$key = $value;
    }
  }

  /**
   * Function to generate access token
   * @return string/mixed
   */
  public static function token()
  {
    $endpoint = ( self::$env == 'live' ) ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials' : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    $credentials = base64_encode( self::$appkey.':'.self::$appsecret );
    $response = wp_remote_get( 
      $endpoint, 
      array(
        'headers' => array(
          'Authorization' => 'Basic ' . $credentials
        )
      )
    ); 

    return is_wp_error( $response ) ? 'Invalid' : json_decode( $response['body'] )->access_token;
  }

  /**
   * Function to process response data for validation
   * @param String $callback - Optional callable function to process the response - must return boolean
   * @return array
   */ 
  public static function validate( $callback, $data )
  {
    if( is_null( $callback) ){
      return array( 
        'ResponseCode'            => 0, 
        'ResponseDesc'            => 'Success',
        'ThirdPartyTransID'       => isset( $data['transID'] ) ? $data['transID'] : 0
       );
    } else {
        if ( !call_user_func_array( $callback, array( $data ) ) ) {
          return array( 
            'ResponseCode'        => 1, 
            'ResponseDesc'        => 'Failed',
            'ThirdPartyTransID'   => isset( $data['transID'] ) ? $data['transID'] : 0
           );
        } else {
          return array( 
            'ResponseCode'        => 0, 
            'ResponseDesc'        => 'Success',
            'ThirdPartyTransID'   => isset( $data['transID'] ) ? $data['transID'] : 0
           );
        }
    }
  }

  /**
   * Function to process response data for confirmation
   * @param String $callback - Optional callable function to process the response - must return boolean
   * @return array
   */ 
  public static function confirm( $callback, $data )
  {
    if( is_null( $callback) ){
      return array( 
        'ResponseCode'          => 0, 
        'ResponseDesc'          => 'Success',
        'ThirdPartyTransID'     =>  isset( $data['transID'] ) ? $data['transID'] : 0
       );
    } else {
      if ( !call_user_func_array( $callback, array( $data ) ) ) {
        return array( 
          'ResponseCode'        => 1, 
          'ResponseDesc'        => 'Failed',
          'ThirdPartyTransID'   => isset( $data['transID'] ) ? $data['transID'] : 0
         );
      } else {
        return array( 
          'ResponseCode'        => 0, 
          'ResponseDesc'        => 'Success',
          'ThirdPartyTransID'   => isset( $data['transID'] ) ? $data['transID'] : 0
         );
      }
    }
  }

  /**
   * Function to register validation and confirmation URLs
   * @param String $env - Environment for which to register URLs
   * @return bool/array
   */
  public static function register( $env = 'sandbox' )
  {
    $endpoint = ( $env == 'live' ) ? 'https://api.safaricom.co.ke/mpesa/c2bwp/v1/registerurl' : 'https://sandbox.safaricom.co.ke/mpesa/c2bwp/v1/registerurl';
    $post_data = array( 
      'ShortCode'         => self::$shortcode,
      'ResponseType'      => 'Cancelled',
      'ConfirmationURL'   => self::$confirm,
      'ValidationURL'     => self::$validate
    );
    $data_string = json_encode( $post_data );

    $response = wp_remote_post( 
      $endpoint, 
      array(
        'headers'           => array(
          'Content-Type'    => 'application/json', 
          'Authorization'   => 'Bearer ' . self::token()
        ), 
        'body'              => $data_string
      )
    );
    return is_wp_error( $response ) ? array( 'errorCode' => 1, 'errorMessage' => 'Could not connect to Daraja' ) : json_decode( $response['body'], true );
  }

  /**
   * Function to process request for payment
   * @param String $phone     - Phone Number to send STK Prompt Request to
   * @param String $amount    - Amount of money to charge
   * @param String $reference - Account to show in STK Prompt
   * @param String $trxdesc   - Transaction Description(optional)
   * @param String $remark    - Remarks about transaction(optional)
   * @return array
   */ 
  public static function request( $phone, $amount, $reference, $trxdesc = 'WooCommerce Payment', $remark = 'WooCommerce Payment' )
  {
    $phone      = preg_replace( '/^0/', '254', str_replace( "+", "", $phone ) );

    $endpoint   = ( self::$env == 'live' ) ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest' : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

    $timestamp  = date( 'YmdHis' );
    $password   = base64_encode( self::$headoffice.self::$passkey.$timestamp );

    $post_data  = array( 
      'BusinessShortCode'   => self::$headoffice,
      'Password'            => $password,
      'Timestamp'           => $timestamp,
      'TransactionType'     => ( self::$type == 4 ) ? 'CustomerPayBillOnline' : 'BuyGoodsOnline',
      'Amount'              => round( $amount ),
      'PartyA'              => $phone,
      'PartyB'              => self::$shortcode,
      'PhoneNumber'         => $phone,
      'CallBackURL'         => self::$reconcile,
      'AccountReference'    => $reference,
      'TransactionDesc'     => $trxdesc,
      'Remark'              => $remark
    );

    $data_string  = json_encode( $post_data );
    $response     = wp_remote_post( 
      $endpoint, 
      array(
        'headers' => array(
          'Content-Type' => 'application/json', 
          'Authorization' => 'Bearer ' . self::token()
        ), 
        'body'    => $data_string
      )
    );
    return is_wp_error( $response ) ? array( 'errorCode' => 1, 'errorMessage' => 'Could not connect to Daraja' ) : json_decode( $response['body'], true );
  }

/**
   * Function to process response data for reconciliation
   * @param String $callback - Optional callable function to process the response - must return boolean
   * @return bool/array
   */            
  public static function reconcile( $callback, $data )
  {
    if( is_null( $data ) ){
      $response = json_decode( file_get_contents( 'php://input' ), true );
      $response = isset( $response['Body'] ) ? $response['Body'] : array();
    } else {
      $response = $data;
    }
    
    return is_null( $callback ) ? array( 'resultCode' => 0, 'resultDesc' => 'Reconciliation successful' ) : call_user_func_array( $callback, array( $response ) );
  }

  /**
   * Function to process response data if system times out
   * @param String $callback - Optional callable function to process the response - must return boolean
   * @return bool/array
   */ 
  public static function timeout( $callback = null, $data = null )
  {
    if( is_null( $data ) ){
      $response = json_decode( file_get_contents( 'php://input' ), true );
      $response = isset( $response['Body'] ) ? $response['Body'] : array();
    } else {
      $response = $data;
    }

    if( is_null( $callback ) ){
      return true;
    } else {
      return call_user_func_array( $callback, array( $response ) );
    }
  }
}