@extends('email.email_layout')
@section('body')
            <!-- END MODULE: Content 12 -->
            <!-- BEGIN MODULE: Content 9 -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
                <tbody>
                    <tr>
                        <td width="100%" valign="top" bgcolor="#ffffff" style="background-color: #ffffff" pc-default-class="pc-sm-p-25-30-35 pc-xs-p-15-20-25 " pc-default-padding="30px 40px 40px ">
                            <table border="0 " cellpadding="0 " cellspacing="0 " width="100% " role="presentation ">
                                <tbody>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td height="20 " style="font-size: 1px; line-height: 1px; ">
                                            &nbsp;</td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td class="pc-fb-font " style="line-height: 16px; font-family: 'Montserrat', Montserrat, Helvetica, Arial, sans-serif; letter-spacing: 0.5px; " valign="top ">
                                            <h3 style="text-align: center; margin-bottom: 40px; font-weight: 600; font-size: 18px; ">Password Reset</h3>
                                            <p style="line-height: 16px; font-family: 'Montserrat', Montserrat, Helvetica, Arial, sans-serif; letter-spacing: 0.5px; font-size: 14px; margin-bottom: 12px; font-weight:
                            normal; ">Hi {{$first_name}},</p>
                                            <span style="font-size: 14px; font-weight: normal; font-style: normal; color: #000000 ">
                                                <p>
                                                    You are receiving this email because we received a password reset request for your account. <br>
                                                </p>
                                                <span style="line-height: 24px; ">&nbsp;</span>
                                                <p>
                                                    Your password reset token is <b>{{$token}}</b>
                                                </p>
                                                <span style="line-height: 24px; ">&nbsp;</span>
                                                <p>
                                                    This password reset token will expire in {{$duration}} minutes.
                                                </p>
                                                <span style="line-height: 24px; ">&nbsp;</span>
                                            <p style="line-height: 26px; ">If you did not initiate this request, please contact us at <a href="https://1automech.com " style="text-decoration: none;
                            color: #3A89F8; ">1automech.com</a> or email us on <a href="mailto:hello@1automech.com">hello@1automech.com</a> and we will be happy to help!</p>
                                            </span>


                                            <p style="line-height: 26px; font-size: 14px; margin-top: 32px; ">Regards,</p>
                                            <p style="line-height: 26px; font-size: 14px; margin-bottom: 27px; ">1automech</p>

                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>

        </td>
    </tr>
    </tbody>
    </table>
    <!-- container for content -->
    
@endsection