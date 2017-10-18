@component('mail::message')
    <center>
        {{ Html::image('images/view1_principal_r_interlaced.png', 'Selfy screenshot', array('width' => '60%')) }}
</center>

<b>Hola / Hello / Olá!</b> <br />

<b>Español:</b><br /><br />
{{ $name }}, hemos renovado Selfy para que puedas seguir disfrutando y compartiendo fotos con tus amigos. <br /><br />
El chat ha sido mejorado y hemos corregido varios errores. Con la nueva version para Android podrás conocer a más personas. <br /><br />
Te esperamos de nuevo. <br /><br />PD: Hemos reinventado los Selfy-retos, que estáran disponibles pronto para Windows Phone, ya puedes probarlos si dispones de un Android.
<hr />
<b>English:</b><br /><br />
{{ $name }}, we have renewed Selfy so you can enjoy and share photos with your friends. <br /><br />
The chat has been improved and we have fixed several errors. With the new version for Android you can meet more people. <br /><br />
We are waiting for you again. <br /><br /> We have reinvented the Selfy-challenges, which will be available soon for Windows Phone, you can try them if you have an Android.
<hr />
<b>Portugués:</b><br /><br />
{{ $name }}, nós renovamos o Selfy para que você possa desfrutar e compartilhar fotos com seus amigos.<br /><br />
O bate-papo foi melhorado e corrigimos vários erros. Com a nova versão do Android, você pode conhecer mais pessoas.<br /><br />
Nós estamos esperando por você novamente.<br /><br /> Nós reinventamos os desafios Selfy, que estarão disponíveis em breve para o Windows Phone, você pode experimentá-los se você tiver um Android.
<hr />
<br /><br />
    <center>
        {{ Html::image('images/apple-icon-180x180.png', 'Selfy icon', array('width' => '30%')) }}
    </center>

@component('mail::button', ['url' => 'https://play.google.com/store/apps/details?id=com.sparkly.selfy', 'color' => 'blue'])
    Selfy Android
@endcomponent

@component('mail::button', ['url' => 'https://www.microsoft.com/store/p/selfy/9wzdncrdc51s', 'color' => 'blue'])
    Selfy Windows Phone
@endcomponent
<br /><br />
Website:
http://getselfy.net
<br /><br />
Twitter: http://twitter.com/getselfy
<br /><br />
The Selfy team.
@endcomponent
