<!DOCTYPE html>
<html>
<head
>   

    <meta charset="UTF-8">
    <title>ATTESTATION DE STAGE</title>
    <style>
        a {
    font-style: none;
    color: white; /* set the color to blue */
}
a:visited {
    color: white; /* set the color to purple when the link is visited */
}
html{
    border: 5px solid black;
    width: 100vw;
    height: 100vh;
    margin: 0;
    padding: 0;
}
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding-top: 20px;
            padding-bottom: 30px;
            padding-left: 20px;
            padding-right: 20px;
            /* space between lines  */
            line-height: 2;
            
            /* full height */
            height: 100vh;
            /* fell width */
            width: 100vw;
           
        }
        .logo {
            margin-bottom: 10px;
            display: flex;
            flex-direction: row;
            /* space between */
            /* justify-content: center; */
            align-items: space-between;

    
}
.flexer{
    /* flex and center the container */
    display: flex;
            flex-direction: column;

            justify-content: center;
            align-items: center;
            /* center vertically */
            align-content: center;

}
.logoimg{
    /* turn the white parts of the imgae to black */
    width: 50px;
    height: auto;
}
        .container {
            /* center  */
            /* position: absolute; 
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); */
            
            width: 100%;
            
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 36px;
            margin: 0;
            padding: 0;
        }
        .content {
            text-align: center;
            padding-top: 20px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;

            justify-content: center;
            align-items: center;
            /* center vertically */
            align-content: center;
            /* padding-left: 60px */

        }
        .content p {
            text-align: center;
            font-size: 18px;
            align-content: center;
            margin: 0;
            padding: 0;
            line-height: 1.5;
            font-style: italic;
            font-weight: 100;
        }
        .qr-code {
            text-align: center;
            width: 100px;
            height: 100px;
            float: right;
        }
        .qr-code img {
            max-width: 100%;
            height: auto;
        }
        .footer {
            display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
            position:absolute;
            bottom: 0;
            width: 100%;
            font-size: 12px;

        }
        .footer p {
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
   
    <div class="flexer">
         <div class="logo">
         <a href="{{ $frontendUrl }}" target="_blank" >
        <img class="logoimg" src="https://raw.githubusercontent.com/AmineZCS/internship-management-vue/main/src/assets/IABlack.png" >
    </a>
    </div>
    <div class="container">
        <div class="header">
            <small>République Algérienne Démocratique et Populaire</small>
            <br>
            <small> Ministère de l'Enseignement Supérieur et de la Recherche Scientifique</small>
            <br>
            <small> Université Abdelhamid Mehri Constantine 2 </small>
            
            <h1>ATTESTATION DE STAGE</h1>
            <small>Accréditée par InternshipAxis.</small>
        </div>
        <div class="content" style="margin-top: 50px; margin-bottom: 90px">
        
            <div>
            <p>Je, soussigné(e): <strong>{{$supervisor->fname}} , {{$supervisor->lname}}</strong> responsable de stage  de:<strong> {{$internship->position }} </strong></p>
            </div>
             <br>
            <div>
            <p>atteste que l'étudiant(e) <strong>{{$student->fname}} , {{$student->lname}}</strong> inscrit(e) à l' Université Abdelhamid Mehri Constantine 2</p>
            </div>
             <br>
           <div>
           <p>a effectué un stage de formation au sein de notre entreprise <strong>{{$company->name}}</strong> du <strong>{{$internship->start_date}}</strong> au <strong>{{$internship->end_date}}</strong></p>
   
           </div>
        </div>
        
        
        <br>
        <hr>
        <br>
        <small> <i>Cette attestation est délivrée pour servir et faire valoir ce que de droit</i></small>
        <div class="qr-code">

        <a href="{{ $url }}" target="_blank" >
            <img src="{{ $qrCodePath }}" alt="QR Code">
</a>
        </div>
    </div>
    </div>
    <div class="footer">
            Certificate ID: {{ $token }}
    </div>

    

    
</body>
</html>