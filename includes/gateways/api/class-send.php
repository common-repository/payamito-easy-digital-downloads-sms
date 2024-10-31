<?php

/**
 *  Class Gateways Payamito
 *
 * @since   1.0.0
 */
defined( 'ABSPATH' ) || exit;

class Payamito_Edd_Send extends Payamito_Edd_Send_Abstract
{
	/**
	 * phone number variable
	 *
	 * @since 1.0
	 * @var object
	 */
	public $toNum;
	/**
	 *pattern code variable
	 *
	 * @since 1.0
	 * @var object
	 */
	public $pattern_code;
	/**
	 *options variable
	 *
	 * @since 1.0
	 * @var object
	 */
	public $options;

	/**
	 *send type variable
	 *
	 * @since 1.0
	 * @var object
	 */
	public $send_type;

	public function __construct()
	{
		global $edd_payamito_options;
		$this->options = $edd_payamito_options;
	}

	/**
	 * Respanse gateway message
	 *     * @param $r param
	 */
	public function Message( $r )
	{
		if ( $r === true ) {
			return __( 'success', 'payamito-edd' );
		}
		$r = intval( $r );

		$messages = [
			12  => "مدارک کاربر کامل نمی باشد",
			11  => ".ارسال نشده",
			10  => "کاربرمورد نظرفعال نمی باشد.",
			7   => "متن حاوی کلمه فیلتر شده می باشد، با واحد اداری تماس بگیرید",
			6   => "سامانه در حال بروزرسانی می باشد.",
			5   => "شماره فرستنده معتبرنمی باشد",
			4   => "محدودیت در حجم ارسال",
			3   => "حدودیت در ارسال روزانه",
			2   => ":اعتبار کافی نمی باشد",
			1   => "درخواست با موفقیت انجام شد",
			0   => "نام کاربری یا رمز عبور صحیح نمی باشد",
			- 1 => "دسترسی برای استفاده از این وبسرویس غیرفعال است، با پشتیبانی تماس بگیرید.",
			- 2 => "محدودیت تعداد شماره، محدودیت هر بار ارسال 1 شماره موبایل می باشد",
			- 3 => "خط ارسالی در سیستم تعریف نشده است، با پشتیبانی سامانه تماس بگیرید.",
			- 4 => "کد متن ارسالی صحیح نمی باشد و یا توسط مدیر سامانه تایید نشده است.",
			- 5 => "تن ارسالی با توجه به متغیر های مشخص شده در متن پیشفرض همخوانی ندارد",
			- 6 => "خطای داخلی رخ داده است با پشتیبانی تماس بگیرید",
			- 7 => "خطایی در شماره فرستنده رخ داده است با پشتیبانی تماس بگیرید",
			- 100,
			' حساب شما امکان ارسال بدون الگو  را ندارد ',
		];

		foreach ( $messages as $index => $m ) {
			if ( $index == $r ) {
				return $m;
			}
		}

		return __( "Not Fount Message", "payamito-edd" );
	}

	/**
	 * get gateway name
	 *     * @param $r param
	 */
	public function getName()
	{
		return "Payamito";
	}

	/**
	 * get params
	 */
	public function getParams()
	{
		return [
			"fromNum"        => $this->fromNum,
			"toNum"          => $this->toNum,
			"messageContent" => $this->messageContent,
			"pattern_code"   => $this->pattern_code,
			"pass"           => $this->pass,
		];
	}

	/**
	 * set params
	 */
	public function setParams()
	{
		list( $toNum, $message, $pattern_code, $payment_id ) = func_get_args();

		$this->toNum = $toNum;

		$this->message = $message;

		$this->pattern_code = $pattern_code;

		$this->payment_id = $payment_id;
	}

	/**
	 * Send sms
	 */
	public function Send( $OTP = false )
	{
		if ( $this->send_type == 'send_by_pattern' ) {
			$this->messageContent = parent::generate_message( $OTP );

			$result = payamito_send_pattern( $this->toNum, $this->messageContent, $this->pattern_id, payamito_edd()::$slug );
			if ( $result > 2000 ) {
				$this->success = true;

				$result = true;
			} else {
				$result = json_decode( $result );
			}

			return [ "result"  => $result,
			         "message" => $this->Message( $result ),
			         'mobile'  => $this->mobile,
			         "OTP"     => $this->OTP,
			];
		} else {
			$input_data = parent::generate_message_string( $OTP );

			$result = payamito_send( [ $this->toNum ], $input_data );

			$result = json_decode( $result );

			if ( $result == '1' ) {
				$result = true;
			}

			return [ "result"  => $result,
			         "message" => $this->Message( $result ),
			         'mobile'  => $this->mobile,
			         "OTP"     => $this->OTP,
			];
		}
	}

	/**
	 * set send type
	 */
	public function set_send_type( $send_type )
	{
		$this->send_type = $send_type;
	}
}
