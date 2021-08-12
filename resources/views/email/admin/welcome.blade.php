@extends('email.email_layout')
@section('body')
                    <!-- END MODULE: Content 12 -->
                    <!-- BEGIN MODULE: Content 9 -->
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
                        <tbody>
                            <tr>
                                <td class="" width="100%" valign="top" bgcolor="#ffffff" style="background-color: #ffffff" pc-default-class="pc-sm-p-25-30-35 pc-xs-p-15-20-25 " pc-default-padding="30px 40px 40px ">
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
                                                    <h3 style="text-align: center; margin-bottom: 40px; font-weight: 600; font-size: 18px; ">Welcome to 1automech Admin</h3>
                                                    <p style="line-height: 16px; font-family: 'Montserrat', Montserrat, Helvetica, Arial, sans-serif; letter-spacing: 0.5px; font-size: 14px; margin-bottom: 16px; font-weight:
                                    normal; ">Hi {{$first_name}},</p>
                                                    <span style="font-size: 14px; font-weight: normal; font-style: normal; color: #000000; line-height: 24px; ">
                                                        <p style="margin-bottom: 16px; ">
                                                            Congratulations, you have been registered as an admin on 1automech dashboard.
                                                        </p>
                                                        <p style="margin-bottom: 16px; ">
                                                            Find your login credentials below: <br>
                                                            <span style="margin-bottom: 16px; color: #3A89F8; ">
                                                                Email: <b>{{$request->email}}</b><br>
                                                                Password: <b>{{$request->password}}</b>
                                                            </span>
                                                        </p>
                                                        <p style="margin-bottom: 16px; ">
                                                            Do not forget to keep these credentials safe, and change your password as soon as possible                                                            
                                                        </p>

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