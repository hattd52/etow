<!DOCTYPE html>
<html lang=&quot;en-US&quot;>
<head>
    <meta charset=&quot;utf-8&quot;>
    <title>Forgot Password</title>
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
            <p>We received your reset password request from the eTow admin site.</p>
            <p>Token to reset password is: <?= $data['reset_token'] ?> </p>
            <p>Please click the link and input this token to reset password : <a href="<?= $data['url_reset'] ?>"><?= $data['url_reset'] ?></a></p>
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