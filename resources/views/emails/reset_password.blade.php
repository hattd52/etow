<!DOCTYPE html>
<html lang=&quot;en-US&quot;>
<head>
    <meta charset=&quot;utf-8&quot;>
    <title>Reset Password</title>
</head>
<body>
<table style="width: 100%; margin: auto;">
    <tr>
        <td></td>
    </tr>
    <tr>
        <td>Dear <?= $data['name'] ?>, <br/></td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td>
            <p>We received your reset password request from the eTow app.
            Your new password is: <?= $data['new_password'] ?> </p>
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td>
            Best Regard.
        </td>
    </tr>
    <tr>
        <td>
            Thank you,
        </td>
    </tr>
</table>


</body>
</html>