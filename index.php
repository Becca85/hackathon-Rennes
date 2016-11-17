<!doctype html>
<html class="no-js" lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Hackathon Code Academie Rennes</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="html-coding.svg">
    <!-- Place favicon.ico in the root directory -->
    <link rel="icon" href="html-coding.svg">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="stylesheet" href="css/main.css">
</head>

<body>


<?php
    include "config.php";
    $info_message = [];
    $error_message = [];

    // tableau compteur initialisé a 0
    $tabcompteur= array(
        'Developpeur web' => 0,
        'Big Data'=> 0,
        'Designer'=> 0,
        'Referenciel metier'=>0
    );

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Une inscription est en cours.
        $params = [];
        $params['nom'] = $_POST['nom'];
        $params['prenom'] = $_POST['prenom'];
        $params['email'] = $_POST['email'];
        $params['metier'] = $_POST['metier'];
        $params['message'] = $_POST['message'];


        try{//Connexion
            $connexion= new PDO("mysql:host=$serveur;dbname=$database",$login,$pass);
            $connexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            
            $tableau_result = select($connexion);
            $success = add($tableau_result,$connexion, $params);
            if($success){
                $msg = "Vous êtes la ".(count($tableau_result)+1)."e personne inscrite";
                array_push($info_message, $msg);
            }
        }
        catch (PDOException $e){
            $msg = 'Echec de la connexion : '.$e->getMessage();
            array_push($error_message, $msg);
        }
    }
    
    /* Fonction selectionnant l'ensemble des utilisateurs inscrits.
        @params $connexion : La connection PDO
    */
    function select(PDO $connexion){
        $requete=$connexion->prepare("
            SELECT * FROM Inscrits");
        $requete->execute();
        $resultat=$requete->fetchall();     
        return $resultat;
    }

    /* Fonction ajoutant un inscrit
        @params $tableau_result : Un tableau issu du select()
        @params $connexion : La connection PDO
        @params $params : Un tableau avec la structure ['nom'=>.., 'prenom'=>.., 'email'=>.., 'metier'=>.., 'message'=>..]
    */
    function add($tableau_result,$connexion, $params){
            error_log("Using add function width params : [".join(',', $params)."]");
            $success = false;
            //Je recupère mes variables globales pour pouvoir les utiliser dans cette fonction
            global $tabcompteur, $tabmax, $error_message, $info_message;
            $nom = $params['nom'];
            $prenom = $params['prenom'];
            $email = $params['email'];
            $metier = $params['metier'];
            $message = $params['message'];
            if($nom != null && $prenom != null && $email != null && $metier != null){
                if(isset($email)){
                    if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)){
                        $trouve = false;
                        foreach($tableau_result as $row){
                            $bd_nom = $row['nom'];
                            $bd_prenom = $row['prenom'];
                            $bd_mail = $row['email'];
                            $bd_metier = $row['metier'];
                        

                            if(($bd_nom == $nom && $bd_prenom== $prenom) || $bd_mail== $email) {
                                $trouve=true;
                                $msg = 'Votre participation est déja enregistrée';
                                array_push($error_message, $msg);
                            }
                        }

                        if(!$trouve){
                            compteur();

                            if ($tabmax[$metier]>$tabcompteur[$metier]){
                            $sql = "INSERT INTO Inscrits(nom, prenom, email, metier, message) VALUES (:nom,:prenom,:email,:metier,:message)";
                            // Envoi des données
                            $requete2= $connexion->prepare($sql);
                            $params = array('nom'=>$nom, 'prenom'=>$prenom, 'email'=>$email, 'metier'=>$metier, 'message'=>$message);
                            $requete2->execute($params);
                            $msg = "Votre inscription a bien été effectuée";
                            array_push($info_message, $msg);$success = true;
                            $success = true;
                            }

                            else {
                                $msg = "Navré, nous avons suffisament de ".$metier." inscrits";
                                array_push($error_message, $msg);
                            }


                        }
                    }
                       
                   
                    else{
                        $msg= 'L\'adresse mail ' .$email. ' n\'est pas valide.';
                        array_push($error_message, $msg);
                    }
                }
            }

            else {
                $msg = 'Vous n\'avez pas rempli tous les champs';
                array_push($error_message, $msg);
            }

            return $success;
    }


    // Fonction definissant le tabcompteur.
    function compteur() {
        //Je recupère mes variables globales pour pouvoir les utiliser dans cette fonction
        global $tableau_result;
        global $tabmax;
        global $tabcompteur;
        // Par rapport au nombre d'inscripts sur un metier 
        foreach ($tableau_result as $key => $row) {
            $tabcompteur[$row['metier']]++;
        }

    }
?>

<?php
    echo "<div class='errorMessages'>";
    foreach ($error_message as $message) {
       echo "-".$message."<br/>";
    }
    echo "</div>";
    echo "<div class='infoMessages'>";
    foreach ($info_message as $message) {
       echo "-".$message."<br/>";
    }
    echo "</div>";
?>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

    <!-- Section de mise en bouche -->
    <div class="first-wrap">
        <div class="first-div">
            <div class="first-inner-div">
                <a href="#anchor" class="js-scrollTo">
                    <h1 class="info">HACKATON CODE ACADEMIE</h1>
                    <h1 class="info">Les 26 et 27 novembre</h1>
                    <h1 class="info">Au 23 rue de l'Aiguillon</h1>
                </a>
            </div>
        </div>
    </div>


    <!-- Colonne de présentation de l'hackaton -->
    <section class="col-md-5 col-md-offset-1 section-present" id="anchor">
        <div class="col-md-12 block" id="anchor">
            <h2 class="col-md-12">{ Présentation ? }</h2>
            <div class="separator-present col-md-8 col-md-offset-1 center-block"></div>
            <p class="col-md-12">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam bibendum, elit ac rhoncus ornare, elit dui mollis risus, id rutrum massa erat vel neque. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed convallis imperdiet rutrum. Etiam sit amet erat odio. Quisque non dui eget tortor finibus facilisis. Proin facilisis eros lectus, ac aliquam justo dapibus non. Nunc porta urna et eros finibus rutrum. Donec blandit commodo accumsan. Morbi at mi a mi ornare ullamcorper non id metus. Aliquam pharetra arcu in tortor pulvinar hendrerit. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Obcaecati ex maiores totam nostrum cupiditate itaque! Cum, explicabo, exercitationem optio dignissimos aperiam placeat nemo, asperiores ea, provident earum vero quas neque.</p>
        </div>
        <div class="col-md-12 block">
            <h2 class="col-md-12">{ Pour qui ? }</h2>
            <div class="separator-present col-md-8 col-md-offset-1 center-block"></div>
            <p class="col-md-12">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam bibendum, elit ac rhoncus ornare, elit dui mollis risus, id rutrum massa erat vel neque. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed convallis imperdiet rutrum. Etiam sit amet erat odio. Quisque non dui eget tortor finibus facilisis. Proin facilisis eros lectus, ac aliquam justo dapibus non. Nunc porta urna et eros finibus rutrum. Donec blandit commodo accumsan. Morbi at mi a mi ornare ullamcorper non id metus. Aliquam pharetra arcu in tortor pulvinar hendrerit. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis expedita ipsum facilis neque at omnis. Voluptatem earum eligendi rerum placeat, fugiat labore, perspiciatis doloremque animi veritatis sapiente voluptate voluptatum, et.</p>
        </div>
        <div class="col-md-12 block">
            <h2 class="col-md-12">{ C'est quoi ? }</h2>
            <div class="separator-present col-md-8 col-md-offset-1 center-block"></div>
            <p class="col-md-12">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam bibendum, elit ac rhoncus ornare, elit dui mollis risus, id rutrum massa erat vel neque. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed convallis imperdiet rutrum. Etiam sit amet erat odio. Quisque non dui eget tortor finibus facilisis. Proin facilisis eros lectus, ac aliquam justo dapibus non. Nunc porta urna et eros finibus rutrum. Donec blandit commodo accumsan. Morbi at mi a mi ornare ullamcorper non id metus. Aliquam pharetra arcu in tortor pulvinar hendrerit. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sunt atque, incidunt sit a quasi aspernatur corrupti aut saepe laudantium ex, possimus culpa soluta eum laboriosam laborum autem veniam totam dicta.</p>
        </div>
        <div class="col-md-12 block">
            <h2 class="col-md-12">{ Comment ? }</h2>
            <div class="separator-present col-md-8 col-md-offset-1 center-block"></div>
            <p class="col-md-12">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam bibendum, elit ac rhoncus ornare, elit dui mollis risus, id rutrum massa erat vel neque. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed convallis imperdiet rutrum. Etiam sit amet erat odio. Quisque non dui eget tortor finibus facilisis. Proin facilisis eros lectus, ac aliquam justo dapibus non. Nunc porta urna et eros finibus rutrum. Donec blandit commodo accumsan. Morbi at mi a mi ornare ullamcorper non id metus. Aliquam pharetra arcu in tortor pulvinar hendrerit. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eum, maiores, quidem. Ullam nulla, modi explicabo. Rerum nesciunt dolorum facere, possimus officiis aliquam asperiores ipsam impedit obcaecati. Excepturi voluptatem, in facilis!</p>
        </div>
    </section>


    <!-- Colonne du nombre de participant et du planning -->
    <section class="col-md-5 section-present">
        <div class="col-md-12 block">

            <h2 class="col-md-12">{ Nombre de places disponibles, <?= array_sum($tabmax)-  array_sum($tabcompteur); ?> ? }</h2>
            <div class="separator-present col-md-8 col-md-offset-1 center-block"></div>
            <?php
                foreach ($tabcompteur as $key => $value) {
                    echo "<div class='col-md-10 col-md-offset-1 participant'>";
                    echo "<p class='col-md-12'>";
                    echo $key, ":",$tabmax[$key]-$value;
                    echo "</p>";
                    echo "</div>";
                }
            ?>
        </div>
        <div class="col-md-12 block">
            <h2 class="col-md-12">{ planning ? } <small>JOURNÉE 1</small></h2>
            <div class="separator-present col-md-8 col-md-offset-1 center-block"></div>
            <h3 class="col-md-12">matin</h3>
            <p class="col-md-12">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam bibendum, elit ac rhoncus ornare, elit dui mollis risus, id rutrum massa erat vel neque. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed convallis imperdiet rutrum. Etiam sit amet erat odio.</p>
            <h3 class="col-md-12">après-midi</h3>
            <p class="col-md-12">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam bibendum, elit ac rhoncus ornare, elit dui mollis risus, id rutrum massa erat vel neque. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        </div>
        <div class="col-md-12 block">
            <h2 class="col-md-12">{ planning ? } <small>JOURNÉE 2</small></h2>
            <div class="separator-present col-md-8 col-md-offset-1 center-block"></div>
            <h3 class="col-md-12">matin</h3>
            <p class="col-md-12">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam bibendum, elit ac rhoncus ornare, elit dui mollis risus, id rutrum massa erat vel neque. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed convallis imperdiet rutrum. Etiam sit amet erat odio.</p>
            <h3 class="col-md-12">après-midi</h3>
            <p class="col-md-12">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam bibendum, elit ac rhoncus ornare, elit dui mollis risus, id rutrum massa erat vel neque. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 block partenaire1">
            <h2 class="col-md-12">{ Partenaires ? }</h2>
            <div class="separator-present col-md-8 col-md-offset-1 center-block"></div>
            <div class="col-md-6 col-xs-6 col-sm-6 partenaire">
                <img class="col-md-12 col-sm-5 col-sm-offset-1 img-responsive" src="img/simplon.png" alt="Simplon.co, école dans le numérique">
                <img class="col-md-12 col-sm-5 col-sm-offset-1 img-responsive" src="img/face.png" alt="Association FACE RENNES">
                <img class="col-md-12 col-sm-5 col-sm-offset-1 img-responsive" src="img/logo_RennesMetropole.png" alt="Rennes Métropôle">
                <img class="col-md-10 col-sm-5 col-sm-offset-1 img-responsive" src="img/pole.png" alt="Pôle Emplois">
            </div>
            <div class="col-md-6 col-xs-6 col-md-6 partenaire">
                <img class="col-md-10 col-sm-5 img-responsive col-md-offset-1" src="img/republique.png" alt="République Française">
                <img class="col-md-8 col-sm-5 col-sm-offset-1 img-responsive col-md-offset-2" src="img/regionbretagne.png" alt="Région Bretagne">
            </div>
        </div>
    </section>


    <!--Contact -->
    <section class="third-wrap container-fluid">
        <div class="fourth-div col-md-10 col-md-offset-1">
            <div class="line-separator"></div>
            <h2 class="text-center">contact</h2>
            <div class="line-separator"></div>
        </div>

        <div class="fifth-div col-md-10 col-md-offset-1">

            <div id="container">
                <div id="contact-wrap">
                    <div id="contact-area">

                        <form method="post" action="index.php">

                            <input type="text" name="nom" id="nom" placeholder="Nom" />

                            <input type="text" name="prenom" id="prenom" placeholder="Prénom" />

                            <input type="text" name="email" id="email" placeholder="Email" />

                            <select name="metier" id="metier">
                                <option value='Developpeur web'>Developpeur web</option>
                                <option value='Réferenciel métier'>Réferentiel métier </option>
                                <option value='Designer'>Designer</option>
                                <option value='Big Data'>Big Data</option>
                            </select>

                            <textarea name="message" rows="20" cols="20" id="message" placeholder="Message"></textarea>

                            <input type="submit" name="submit" value="GO" class="submit-button" />
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </section>


    <!--========= Lieux =======-->
    <div class="six-wrap col-md-12">
        <div class="eight-div col-md-12">
            <div class="fourth-div col-md-10 col-md-offset-1">
                <div class="line-separator2"></div>
                <h2 class="text-center">où nous trouver ?</h2>
                <div class="line-separator2"></div>
            </div>


            <div class="container">
                <div class="row">

                    <section>
                        <div class="container">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <iframe class="adptmap" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2664.907968679869!2d-1.685950984352548!3d48.09271807921983!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x480edfc3577854ef%3A0x8bb7f86bfc5c66c7!2s23+Rue+d&#39;Aiguillon%2C+35200+Rennes!5e0!3m2!1sfr!2sfr!4v1475576043645" width="300" height="550" frameborder="0" style="border: 1px solid black" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>HACKATON CODE ACADEMIE - Les 26 et 27 novembre - Au 23 rue de l'aiguillon</p>
    </footer>

</body>


<script src="js/vendor/jquery-1.11.3.min.js"></script>
<script src="js/plugins.js"></script>
<script src="js/main.js"></script>
<script src="js/vendor/modernizr-2.8.3.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
<script>
    (function (b, o, i, l, e, r) {
        b.GoogleAnalyticsObject = l;
        b[l] || (b[l] =
            function () {
                (b[l].q = b[l].q || []).push(arguments)
            });
        b[l].l = +new Date;
        e = o.createElement(i);
        r = o.getElementsByTagName(i)[0];
        e.src = 'https://www.google-analytics.com/analytics.js';
        r.parentNode.insertBefore(e, r)
    }(window, document, 'script', 'ga'));
    ga('create', 'UA-XXXXX-X', 'auto');
    ga('send', 'pageview');
</script>

<script src="js/vendor/jquery-1.10.2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.js-scrollTo').on('click', function () { // Au clic sur un élément
            var page = $(this).attr('href'); // Page cible
            var speed = 750; // Durée de l'animation (en ms)
            $('html, body').animate({
                scrollTop: $(page).offset().top
            }, speed); // Go
            return false;
        });
    });
</script>

</html>