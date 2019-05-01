<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

    namespace Library\ThanhToan\OnePay;
    use Library\ClientRequest\ClientRequest;
    use Library\ClientRequest\ClientRequestException;
    
    class OnePay{
        ###
        # Merchant ID: vpc_Merchant: ONEPAY
        # AccessCode: vpc_AccessCode: D67342C2
        # Hash Key: A3EFDFABA8653DF2342E8DAC29B51AF0
        # PaymentRequest URL:  https://mtf.onepay.vn/onecomm-pay/vpc.op
        
        const VERSION = 2, CURRENCYCODE = 'VND', COMMAND = 'pay', ACCESSCODE = 'D67342C2', MERCHANT = 'ONEPAY', LOCALE = 'vn';
        
        public function getPaymentURL(PaymentRequestParameter $request){
            $request_params = [
                'vpc_Version' => 2,
                'vpc_Currency' => 'VND',
                'vpc_Command' => 'pay',
                'vpc_AccessCode' => 'D67342C2',
                'vpc_Merchant' => 'ONEPAY',
                'vpc_Locale' => 'vn',
                'vpc_ReturnURL' => $request->returnurl,
                'vpc_MerchTxnRef' => $request->merchtxnref,
                'vpc_OrderInfo' => $request->orderinfo,
                'vpc_Amount' => $request->amount,
                'vpc_TicketNo' => $request->ticketno,
                'AgainLink' => $request->againlink,
                'Title' => $request->title
            ];
            
            ksort($request_params);
            
            $url = '';
            
            foreach($request_params as $key => $value){
                if(strlen($value) > 0 && substr($key,0,4) === 'vpc_' && $key !== 'vpc_SecureHash'){
                    $url .= $key . '='. $value . '&';
                }
            }
            
            $url = rtrim($url, '&');
            
            $securehash = strtoupper(hash_hmac('SHA256', $url, pack('H*', 'A3EFDFABA8653DF2342E8DAC29B51AF0')));
            
            $url .= '&vpc_SecureHash=' . $securehash;
            
            return 'https://mtf.onepay.vn/onecomm-pay/vpc.op?' . $url;
        }
        
        
        public function getPaymentResponse($params){
            #return null: invalid;
            #return != null: valid data
            
            $url = '';
            
            if(!isset($params['vpc_SecureHash']) || !is_string($params['vpc_SecureHash'])){
                throw new OnePayException('Invalid', 1);
            }
            
            foreach($params as $key => $value){
                if(is_string($value) && strlen($value) > 0 && substr($key, 0, 4) === 'vpc_' && $key !== 'vpc_SecureHash'){
                    $url .= $key . '=' . $value . '&';
                }
            }
            
            
            $url = rtrim($url, '&');
            
            $securehash = strtoupper(hash_hmac('SHA256', $url, pack('H*', 'A3EFDFABA8653DF2342E8DAC29B51AF0')));
            
            if($securehash === $params['vpc_SecureHash']){
                $command = isset($params['vpc_Command']) ? $params['vpc_Command'] : null;
                $locale = isset($params['vpc_Locale']) ? $params['vpc_Locale'] : null;
                $currencycode = isset($params['vpc_CurrencyCode']) ? $params['vpc_CurrencyCode'] : null;
                $merchtxnref = isset($params['vpc_MerchTxnRef']) ? $params['vpc_MerchTxnRef'] : null;
                $merchant = isset($params['vpc_Merchant']) ? $params['vpc_Merchant'] : null;
                $orderinfo = isset($params['vpc_OrderInfo']) ? $params['vpc_OrderInfo'] : null;
                $amount = isset($params['vpc_Amount']) ? $params['vpc_Amount'] : null;
                $txnresponsecode = isset($params['vpc_TxnResponseCode']) ? $params['vpc_TxnResponseCode'] : null;
                $transactionno = isset($params['vpc_TransactionNo']) ? $params['vpc_TransactionNo'] : null;
                $message = isset($params['vpc_Message']) ? $params['vpc_Message'] : null;
                $additiondata = isset($params['vpc_AdditionData']) ? $params['vpc_AdditionData'] : null;
                $securehash = isset($params['vpc_SecureHash']) ? $params['vpc_SecureHash'] : null;
                
                switch($txnresponsecode){
                    case 0:
                        $message = 'Giao dịch thành công';
                        break;
                    case 1:
                        $message = 'Giao dịch không thành công. Ngân hàng phát hành thẻ từ chối cấp phép cho giao dịch. Vui lòng liên hệ ngân hàng theo số điện thoại sau mặt thẻ để biết chính xác nguyên nhân Ngân hàng từ chối';
                        break;
                    case 3:
                        $message = 'Giao dịch không thành công, có lỗi trong quá trình cài đặt cổng thanh toán. Vui lòng liên hệ với OnePAY để được hỗ trợ (Hotline 1900 633927)';
                        break;
                    case 4:
                        $message = 'Giao dịch không thành công, có lỗi trong quá trình cài đặt cổng thanh toán. Vui lòng liên hệ với OnePAY để được hỗ trợ (Hotline 1900 633927)';
                        break;
                    case 5:
                        $message = 'Giao dịch không thành công, số tiền không hợp lệ. Vui lòng liên hệ với OnePAY để được hỗ trợ (Hotline 1900 633 927)';
                        break;
                    case 6:
                        $message = 'Giao dịch không thành công, loại tiền tệ không hợp lệ. Vui lòng liên hệ với OnePAY để được hỗ trợ (Hotline 1900 633 927)';
                        break;
                    case 7:
                        $message = 'Giao dịch không thành công, loại tiền tệ không hợp lệ. Vui lòng liên hệ với OnePAY để được hỗ trợ (Hotline 1900 633 927)';
                        break;
                    case 8:
                        $message = 'Giao dịch không thành công. Số thẻ không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 9:
                        $message = 'Giao dịch không thành công. Tên chủ thẻ không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 10:
                        $message = 'Giao dịch không thành công. Thẻ hết hạn/Thẻ bị khóa. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 11:
                        $message = 'Giao dịch không thành công. Thẻ chưa đăng ký sử dụng dịch vụ thanh toán trên Internet. Vui lòng liên hê ngân hàng theo số điện thoại sau mặt thẻ để được hỗ trợ.';
                        break;
                    case 12:
                        $message = 'Giao dịch không thành công. Ngày phát hành/Hết hạn không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 13:
                        $message = 'Giao dịch không thành công. Ngày phát hành/Hết hạn không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 21:
                        $message = 'Giao dịch không thành công. Số tiền không đủ để thanh toán. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 22:
                        $message = 'Giao dịch không thành công. Thông tin tài khoản không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 23:
                        $message = 'Giao dịch không thành công. Tài khoản bị khóa. Vui lòng liên hê ngân hàng theo số điện thoại sau mặt thẻ để được hỗ trợ';
                        break;
                    case 24:
                        $message = 'Giao dịch không thành công. Thông tin thẻ không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 25:
                        $message = 'Giao dịch không thành công. OTP không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 253:
                        $message = 'Giao dịch không thành công. Quá thời gian thanh toán. Vui lòng thực hiện thanh toán lại';
                        break;
                    case 99:
                        $message = 'Giao dịch không thành công. Người sử dụng hủy giao dịch';
                        break;
                    case 100:
                        $message = 'Giao dịch đang tiến hành hoặc chưa thanh toán';
                        break;
                    case 300:
                        $message = 'Giao dịch pending';
                        break;
                }
                
                return new PaymentResponseResult($command, $locale, $currencycode, $merchtxnref, $merchant, $orderinfo, $amount, $txnresponsecode, $transactionno, $message, $additiondata, $securehash);
            }else{
                throw new OnePayException('Giao dịch không hợp lệ', 1);
            }
        }
        
        public function queryTransaction(QueryParameter $query){
            #user: op01
            #password: op123456
            
            $params = [
                'vpc_Command' => 'queryDR',
                'vpc_Version' => 1,
                'vpc_MerchTxnRef' => $query->MerchTxnRef,
                'vpc_Merchant' => 'ONEPAY',
                'vpc_AccessCode' => 'D67342C2',
                'vpc_User' => 'op01',
                'vpc_Password' => 'op123456'
            ];
            
//            
//            $url = '';
//            
//            foreach($params as $key => $value){
//                if(strlen($value) > 0 && substr($key, 0, 4) === 'vpc_'){
//                    $url .= $key . '=' . $value . '&';
//                }
//            }
//            
//            $url = rtrim($url, '&');
//            
//            $securehash = strtoupper(hash_hmac('SHA256', $url, pack('H*', 'A3EFDFABA8653DF2342E8DAC29B51AF0')));
//            
//            $params['vpc_SecureHash'] = $securehash;
//            
            
            
            $query = http_build_query($params);
            
            
            try{
                $request = new ClientRequest('https://mtf.onepay.vn/onecomm-pay/Vpcdps.op' . '?' . $query);
                $request->setOption(CURLOPT_RETURNTRANSFER, 1);
                
                $result = $request->execute();
                
                $result = ltrim($result, '?');
                
                $output = [];
                
                parse_str($result, $output);
                
                $exists = isset($output['vpc_DRExists']) && $output['vpc_DRExists'] === 'Y' ? true : false;
                
                $responsecode = isset($output['vpc_TxnResponseCode']) ? $output['vpc_TxnResponseCode'] : null;
                
                $additiondata = isset($output['vpc_AdditionData']) ? $output['vpc_AdditionData'] : null;
                
                $amount = isset($output['vpc_Amount']) ? $output['vpc_Amount'] : null;
                
                $authenticationdate = isset($output['vpc_AuthenticationData']) ? $output['vpc_AuthenticationData'] : null;
                
                $command = isset($output['vpc_Command']) ? $output['vpc_Command'] : null;
                
                $currencycode = isset($output['vpc_CurrencyCode']) ? $output['vpc_CurrencyCode'] : null;
                
                $locale = isset($output['vpc_Locale']) ? $output['vpc_Locale']: null;
                
                $merchtxnref = isset($output['vpc_MerchTxnRef']) ? $output['vpc_MerchTxnRef'] : null;
                
                $merchant = isset($output['vpc_Merchant']) ? $output['vpc_Merchant'] : null;
                
                $orderinfo = isset($output['vpc_OrderInfo']) ? $output['vpc_OrderInfo'] : null;
                
                $transactionno = isset($output['vpc_TransactionNo']) ? $output['vpc_TransactionNo'] : null;
                
                $message = '';
                
                switch($responsecode){
                    case 0:
                        $message = 'Giao dịch thành công';
                        break;
                    case 1:
                        $message = 'Giao dịch không thành công. Ngân hàng phát hành thẻ từ chối cấp phép cho giao dịch. Vui lòng liên hệ ngân hàng theo số điện thoại sau mặt thẻ để biết chính xác nguyên nhân Ngân hàng từ chối';
                        break;
                    case 3:
                        $message = 'Giao dịch không thành công, có lỗi trong quá trình cài đặt cổng thanh toán. Vui lòng liên hệ với OnePAY để được hỗ trợ (Hotline 1900 633927)';
                        break;
                    case 4:
                        $message = 'Giao dịch không thành công, có lỗi trong quá trình cài đặt cổng thanh toán. Vui lòng liên hệ với OnePAY để được hỗ trợ (Hotline 1900 633927)';
                        break;
                    case 5:
                        $message = 'Giao dịch không thành công, số tiền không hợp lệ. Vui lòng liên hệ với OnePAY để được hỗ trợ (Hotline 1900 633 927)';
                        break;
                    case 6:
                        $message = 'Giao dịch không thành công, loại tiền tệ không hợp lệ. Vui lòng liên hệ với OnePAY để được hỗ trợ (Hotline 1900 633 927)';
                        break;
                    case 7:
                        $message = 'Giao dịch không thành công, loại tiền tệ không hợp lệ. Vui lòng liên hệ với OnePAY để được hỗ trợ (Hotline 1900 633 927)';
                        break;
                    case 8:
                        $message = 'Giao dịch không thành công. Số thẻ không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 9:
                        $message = 'Giao dịch không thành công. Tên chủ thẻ không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 10:
                        $message = 'Giao dịch không thành công. Thẻ hết hạn/Thẻ bị khóa. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 11:
                        $message = 'Giao dịch không thành công. Thẻ chưa đăng ký sử dụng dịch vụ thanh toán trên Internet. Vui lòng liên hê ngân hàng theo số điện thoại sau mặt thẻ để được hỗ trợ.';
                        break;
                    case 12:
                        $message = 'Giao dịch không thành công. Ngày phát hành/Hết hạn không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 13:
                        $message = 'Giao dịch không thành công. Ngày phát hành/Hết hạn không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 21:
                        $message = 'Giao dịch không thành công. Số tiền không đủ để thanh toán. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 22:
                        $message = 'Giao dịch không thành công. Thông tin tài khoản không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 23:
                        $message = 'Giao dịch không thành công. Tài khoản bị khóa. Vui lòng liên hê ngân hàng theo số điện thoại sau mặt thẻ để được hỗ trợ';
                        break;
                    case 24:
                        $message = 'Giao dịch không thành công. Thông tin thẻ không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 25:
                        $message = 'Giao dịch không thành công. OTP không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại';
                        break;
                    case 253:
                        $message = 'Giao dịch không thành công. Quá thời gian thanh toán. Vui lòng thực hiện thanh toán lại';
                        break;
                    case 99:
                        $message = 'Giao dịch không thành công. Người sử dụng hủy giao dịch';
                        break;
                    case 100:
                        $message = 'Giao dịch đang tiến hành hoặc chưa thanh toán';
                        break;
                    case 300:
                        $message = 'Giao dịch pending';
                        break;
                }
                
                return new QueryResult($exists, $responsecode, $message, $additiondata, $amount, $authenticationdate, $command, $currencycode, $locale, $merchtxnref, $merchant, $orderinfo, $transactionno);
            } catch (ClientRequestException $ex) {
                throw new OnePayException($ex->getMessage(), $ex->getCode());
            }
        }
    }