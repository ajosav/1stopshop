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
                                                <td class="pc-fb-font " style="line-height: 22px; font-family: 'Montserrat', Montserrat, Helvetica, Arial, sans-serif; letter-spacing: 0.5px; " valign="top ">
                                                    <h3 style="text-align: center; margin-bottom: 40px; font-weight: 600; font-size: 18px; ">Appointment Confirmation</h3>
                                                    <span style="line-height: 22px; font-family: 'Montserrat', Montserrat, Helvetica, Arial, sans-serif; letter-spacing: 0.5px; font-size: 14px; ">Hello {{$first_name}},</span><br><br>
                                                    <span style="font-size: 14px; font-weight: normal; font-style: normal; color: #000000 ">
                                                        <p>
                                                            We are looking forward to seeing you! <br>
                                                            Thank you for making an appointment for your <b>{{$request['vehicle_type']}} ({{$request['category']}} - {{$request['sub_category']}})</b> on <b>{{$request['date']->format('l')}}</b> at <b>{{$mechanic_shop_name}}.</b> See details of your appointment below.
                                                        </p>
                                                        <span style="line-height: 16px; ">&nbsp;</span>
                                                    <p>If you have any question, just email us at  <a href="mailto:hello@1automech.com">hello@1automech.com</a> and we will be glad to help!</p>
                                                    </span>

                                                    <div class="pc-fb-font " bgcolor="#DBDEE7 " style=" background: #DBDEE7; line-height: 15px; padding: 12px; font-family: 'Montserrat', Montserrat, Helvetica, Arial, sans-serif;
                                    letter-spacing: 0.5px; margin-top: 14px; margin-bottom: 24px; font-weight: 400; font-size: 13px; ">
                                                        <table width="100% ">
                                                            <tr>
                                                                <td style="width: 55%; ">
                                                                    <p style="font-weight: 600; font-size: 14px; "><b>Schedule:</b></p>
                                                                    <span style="line-height: 16px; ">&nbsp;</span>
                                                                    <p><strong>Appointment Date: </strong>{{$request['date']->format('jS F, Y')}}</p>
                                                                    <span style="line-height: 16px; ">&nbsp;</span>
                                                                    <p><strong>Appointment Time: </strong>{{$request['time']}}{{$request['meridian']}}</p>
                                                                    <span style="line-height: 16px; ">&nbsp;</span>
                                                                    <p><strong>Contact Phone Number: </strong>{{$phone_number}}</p>
                                                                </td>
                                                                <td style="border-left: 1px solid #C4C4C4; padding-left: 20px; ">
                                                                    <p style="font-weight: 600; font-size: 14px; "><b>Location:</b></p>
                                                                    <span style="line-height: 16px; ">&nbsp;</span>
                                                                    <p style="line-height: 18px; ">
                                                                        {{$mechanic_address}}
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>

                                                    <table width="100% " style="margin-bottom: 20px; ">
                                                        <tr>
                                                            <td width="50% ">
                                                                <a href="https://www.1automech.com/home/cancel-appointment/{{encrypt($appointment->id)}}/{{$appointment->vistor}}" class="button-link " style="color: #ffffff; background-color: #3A89F8; border-top: 0.5px solid #000000; border-right: 0.5px solid #000000; border-bottom:
                                    0.5px solid #000000; border-left: 0.5px solid #000000; " target="_blank ">
                                                                    <span style=" ">
                                                                        <span>Cancel Appointment</span>
                                                                    </span>
                                                                </a>
                                                            </td>
                                                            <td style="text-align: right; width: 100%; ">
                                                                <a href="# " style="display: inline-block; color: #3A89F8; border-top: 2.5px solid #3A89F8; border-right: 2.5px solid #3A89F8; border-bottom: 2.5px solid
                                    #3A89F8; border-left: 2.5px solid #3A89F8; " target="_blank " class="button-link ">
                                                                    <span style=" ">
                                                                    <span>Add to Calendar</span>
                                                                    </span>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </table>


                                                    <p style="line-height: 26px; font-size: 14px; ">Regards,</p>
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