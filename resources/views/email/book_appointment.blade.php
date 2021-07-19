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
                                                    <h3 style="text-align: center; margin-bottom: 40px; font-weight: 600; font-size: 18px; ">Booking Reservation</h3>
                                                    <span style="line-height: 22px; font-family: 'Montserrat', Montserrat, Helvetica, Arial, sans-serif; letter-spacing: 0.5px; font-size: 14px; ">Hello {{$first_name}},</span><br><br>
                                                    <span style="font-size: 14px; font-weight: normal; font-style: normal; color: #000000 ">
                                                        <p>A reservation was made for a vehicle check. <b>{{$request['description']}}</b></p>
                                                        <span style="line-height: 24px; ">&nbsp;</span>
                                                    <p>If you have any question, just give us a call at (234)8065732572 and we will be glad to help!</p>
                                                    </span>
                                                    <div class="pc-fb-font " bgcolor="#DBDEE7 " style=" background: #DBDEE7; line-height: 15px; padding: 13px; font-family: 'Montserrat', Montserrat, Helvetica, Arial, sans-serif;
                                    letter-spacing: 0.5px; margin-top: 14px; margin-bottom: 24px; font-weight: 400; font-size: 13px; ">
                                                        <p style="font-weight: 600; font-size: 14px; "><b>Schedule</b></p>
                                                        <span style="line-height: 16px; ">&nbsp;</span>
                                                        <p><strong>Appointment Date:</strong> {{$request['date']->format('jS F, Y')}}</p>
                                                        <span style="line-height: 16px; ">&nbsp;</span>
                                                        <p><strong>Appointment Time:</strong> {{$request['time']}}{{$request['meridian']}}</p>
                                                    </div>

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