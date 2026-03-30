<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>OTP Code</title>
</head>

<body style="margin:0; padding:0; background:#f4f6f8; font-family: Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8; padding:20px 0;">
        <tr>
            <td align="center">

                <table width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:500px; background:#ffffff; border-radius:8px; padding:30px;">

                    <tr>
                        <td align="center" style="font-size:20px; font-weight:bold; color:#333;">
                            Your OTP Code
                        </td>
                    </tr>

                    <tr>
                        <td height="15"></td>
                    </tr>

                    <tr>
                        <td style="font-size:14px; color:#555; text-align:center;">
                            Hello {{ $user->name ?? 'User' }},
                        </td>
                    </tr>

                    <tr>
                        <td height="20"></td>
                    </tr>

                    <tr>
                        <td align="center" style="font-size:14px; color:#555;">
                            Your One-Time Password (OTP) is:
                        </td>
                    </tr>

                    <tr>
                        <td height="15"></td>
                    </tr>

                    <tr>
                        <td align="center">
                            <div
                                style="
                                display:inline-block;
                                padding:12px 24px;
                                font-size:28px;
                                font-weight:bold;
                                letter-spacing:4px;
                                background:#f1f3f5;
                                border-radius:6px;
                                color:#111;
                            ">
                                {{ $otp }}
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td height="20"></td>
                    </tr>

                    <tr>
                        <td align="center" style="font-size:13px; color:#777;">
                            This OTP will expire in {{ $expire ?? 5 }} minutes.
                        </td>
                    </tr>

                    <tr>
                        <td height="25"></td>
                    </tr>

                    <tr>
                        <td align="center" style="font-size:12px; color:#999;">
                            If you did not request this code, please ignore this email.
                        </td>
                    </tr>

                    <tr>
                        <td height="20"></td>
                    </tr>

                    <tr>
                        <td align="center" style="font-size:12px; color:#999;">
                            © {{ date('Y') }} {{ config('app.name') }}
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
