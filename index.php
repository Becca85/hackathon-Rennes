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

<body class="container-fluid">


<?php
    include "config.php";
    $info_message = [];
    $error_message = [];
    $tableau_result=[];
    // tableau compteur initialisé a 0
    $tabcompteur= array(
        'Developpeur' => 0,
        'Designer'=> 0,
        'Chef de projet'=> 0,
        'Marketing / Communication'=>0
    );
    try{//Connexion
            $connexion= new PDO("mysql:host=$serveur;dbname=$database",$login,$pass);
            $connexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            
            $tableau_result = select($connexion);
            compteur();
    }
    catch (PDOException $e){
            $msg = 'Echec de la connexion : '.$e->getMessage();
            array_push($error_message, $msg);
    }
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
                           

                            if ($tabmax[$metier]>$tabcompteur[$metier]){
                            $sql = "INSERT INTO Inscrits(nom, prenom, email, metier, message) VALUES (:nom,:prenom,:email,:metier,:message)";
                            // Envoi des données
                            $requete2= $connexion->prepare($sql);
                            $params = array('nom'=>$nom, 'prenom'=>$prenom, 'email'=>$email, 'metier'=>$metier, 'message'=>$message);
                            $requete2->execute($params);
                            $msg = "Votre inscription a bien été effectuée";
                            array_push($info_message, $msg);$success = true;
                            $success = true;
                            $tableau_result = select($connexion);
                            compteur();
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

    <header class="home background background_home">
        <div class="home_container">
            <h1 class="info">HACKATON CODE ACADEMIE</h1>
            <h1 class="info">Le 10-11 Décembre 2016</h1>
            <h1 class="info">Au Loft, 3 Rue de Robien, Rennes</h1>
        </div>
    </header>

    <section class="col-md-12 pres background background_pres" id="anchor">

        <div class="fourth-div col-md-10 col-md-offset-1">
            <div class="line"></div>
            <h2 class="text-center section_title">Presentation</h2>
            <div class="line"></div>
        </div>

        <!-- Colonne de présentation de l'hackathon -->
        <div class="col-md-5 col-md-offset-1 column">

            <article class="col-md-12">
                <h2 class="col-md-12">{ Présentation }</h2>
                <div class="pres_line col-md-8 col-md-offset-1"></div>
                <p class="col-md-12">Bienvenue à la première édition de ce Hackathon qui vous est proposé par <a href="http://code-academie.fr/" target="_blank">la code académie</a>, <a href="https://www.printemps.com/magasins/rennes" target="_blank">printemps</a> et <a href="http://www.coworkinrennes.com/" target="_blank">le Loft</a>. Rejoignez-nous le temps d'un week-end pour exploiter les données que nous fournissent Printemps et innover des concepts et applications à loisir! C'est un challenge multi-compétences qui fait la part belle au travail d'équipe, la communication et la créativité... Sans oublier une bonne dose de technique pour donner vie à vos projets. Le meilleur projet aura droit à une distinction et un cadeau mystère... A vos ordis, prêt ? Incrivez-vous!</p>
            </article>
            <article class="col-md-12">
                <h2 class="col-md-12">{ Un hacka-quoi ? }</h2>
                <div class="pres_line col-md-8 col-md-offset-1"></div>
                <p class="col-md-12">Le mot Hackathon est la combinaison de deux notions : hack et marathon. Un hack, en dépit de sa connotation négative initiale, est désormais le fait de détourner un objet/principe de son utilisation basique pour nous faciliter la vie (d'ailleurs on vous recommande fortement de faire un petit tour sur youtube et de chercher des <a href="https://www.youtube.com/watch?v=567Hkus_MVs" target="_blank">life hack</a>... ça pourrait vous changer la vie dans certaines occaison!). Dans notre cadre, c'est les données de printemps qui sont à hacker pour en tirer de nouvelles perspectives pour eux... ou le grand public. Vous n'êtes pas limités dans la finalité de vos applications. La deuxième notion derrière le hackathon est le marathon. Vous allez devoir coder/designer/échanger/créer et surtout vous éclater pendant 48h non-stop! (sisi, vous avez le droit de dormir quand même). Le but est que vous fournissiez le projet le plus aboutit dans un laps de temps très court et que vous le présentiez devant un jury et les autres participants.</p>
            </article>
            <article class="col-md-12">
                <h2 class="col-md-12">{ Ok super, mais qui peut participer ? }</h2>
                <div class="pres_line col-md-8 col-md-offset-1"></div>
                <p class="col-md-12">Ce challenge s'adresse à tous ceux qui souhaitent travailler en équipe sur l'élaboration d'un nouveau concept à partir de données réelles. Découvrez l'univers des start-up (ou partagez votre expérience) pendant cet événement, vous pouvez même pérenniser votre projet suite à ce week-end ou lui donner vie que le temps de ce week-end... Libre à vous d'en décider! Tous les profils sont les bienvenus, mais pour donner sa chance à tout le monde et pour des raisons de sécurité, nous restreignons le nombre de places en fonction de certains postes : 12 Développeurs, 6 Designers, 6 chef de projets, 6 Marketing/communication pour un total de 30 places. A cela se rajoutera les apprenants de la code académie pour un total de 20 places  Vous ne rentrez pas dans ces cases ? Envoyez-nous votre candidature à l'adresse <a href="mailto:codeacademie@fondationface.org">codeacademie@fondationface.org</a> et nous ferons un retour (n'oubliez pas votre nom, prénom et domaine d'activité). Le public pourra venir à l'inauguration du projet ou à la cérémonie de remise des prix.</p>

                <p class="col-md-12">Clôture des inscriptions le mercredi 7 Décembre</p>

                <p class="col-md-12">Une participation de 25€ par participant sera demandée au début de l'événement. Cette participation nous aidera à couvrir les frais de l'événement et n'aura aucun but lucratif (c'est pour acheter les différents repas, un buffet de clôture et du café... beaucoup de café). FACE Rennes contribue majoritairement aux coûts de l'événement.</p> 
            </article>
            <article class="col-md-12">
                <h2 class="col-md-12">{ Et qui gère tout ça ? }</h2>
                <div class="pres_line col-md-8 col-md-offset-1"></div>
                <p class="col-md-12"><a href="http://code-academie.fr/" target="_blank">La code académie</a> s'occupe de l'aspect logistique et de la communication sur l'événement. Qui sommes-nous ? Nous sommes un centre de formation qui a pour but de former des demandeurs d'emplois au poste de développeur.euses web junior en 8 mois. C'est un projet porté par l'association FACE Rennes (Fondation Agir Contre l'Exclusion Rennes) avec le soutien des institutions publics comme Pôle emploi, la région ou le gouvernement. Nous en sommes à notre première promotion et cet événement a un double but pour nous : permettre à nos apprenants de se confronter au monde professionnel et nous faire connaître. Cet événement n'aurait pas été possible sans la participation de Printemps qui nous fournit ses données et le Loft qui nous mettent à disposition leur locaux. Vous êtes une entreprise souhaitez participez à notre prochaine édition ou aider la code académie ? contactez-nous sur <a href="mailto:codeacademie@fondationface.org">codeacademie@fondationface.org</a>.</p>
            </article>

        </div>

        <!-- Colonne du nombre de participant et du planning -->
        <div class="col-md-5 column">
            <article class="col-md-12">
                <h2 class="col-md-12">{ Nombre de places disponibles : <?= array_sum($tabmax)-  array_sum($tabcompteur); ?> }</h2>
                <div class="pres_line col-md-8 col-md-offset-1 "></div>
                <?php
                    foreach ($tabcompteur as $key => $value) {
                        echo "<div class='col-md-10 col-md-offset-1 participant'>";
                        echo "<p class='col-md-12'>";
                        echo  $tabmax[$key]-$value, " - ",$key;
                        echo "</p>";
                        echo "</div>";
                    }
                ?>
            </article>
            <article class="col-md-12">
                <h2 class="col-md-12">{ planning } <small>JOURNÉE 1</small></h2>
                <div class="pres_line col-md-8 col-md-offset-1 "></div>
                <h3 class="col-md-12">9h00</h3>
                <p class="col-md-12">Début de l'événement autour d'un petit déjeuner. Suite à ce petit déjeuner convivial, les équipes seront constituées et les données leurs seront distribuées</p>
                <h3 class="col-md-12">10h00</h3>
                <p class="col-md-12">Les équipes se lancent sur le projet. Les locaux seront ouvert 24h/24h pendant toute la durée de l'événement. Une permanence sera assurée par la code académie pour toute question éventuelle.</p>
            </article>
            <article class="col-md-12">
                <h2 class="col-md-12">{ planning } <small>JOURNÉE 2</small></h2>
                <div class="pres_line col-md-8 col-md-offset-1 "></div>
                <h3 class="col-md-12">17h00</h3>
                <p class="col-md-12">Présentation des projets par chaque équipe devant un jury et les autres participants. Délibération du jury et annonce du gagnant. Suite à cela, le banquet de clôture commencera.</p>
                <h3 class="col-md-12">19h00</h3>
                <p class="col-md-12">Clôture de l'événement, avec beaucoup de remerciements!</p>
            </article>
            <article class="col-md-12 partenaire">
                <h2 class="col-md-12">{ Partenaires }</h2>
                <div class="pres_line col-md-8 col-md-offset-1"></div>
                <div class="col-md-6 col-xs-6 col-sm-6">
                    <img class="col-md-12 col-sm-5 col-sm-offset-1 img-responsive" src="img/face.png" alt="Association FACE RENNES">
                    <img class="col-md-10 col-sm-5 col-sm-offset-1 img-responsive" src="img/loft.png" alt="Le loft, espace de coworking">
                    <img class="col-md-12 col-sm-5 col-sm-offset-1 img-responsive" src="img/logo_RennesMetropole.png" alt="Rennes Métropôle">
                    <img class="col-md-10 col-sm-5 col-sm-offset-1 img-responsive" src="img/pole.png" alt="Pôle Emplois">
                </div>
                <div class="col-md-6 col-xs-6 col-md-6">
                    <img class="col-md-10 col-sm-5 col-sm-offset-1 img-responsive" src="img/logo-printemps.jpg" alt="Printemps, chaine de magasin de vêtements et cosmétiques">
                    <img class="col-md-12 col-sm-5 col-sm-offset-1 img-responsive" src="img/simplon.png" alt="Simplon.co, école dans le numérique">
                    <img class="col-md-10 col-sm-5 img-responsive col-md-offset-1" src="img/republique.png" alt="République Française">
                    <img class="col-md-8 col-sm-5 col-sm-offset-1 img-responsive col-md-offset-2" src="img/regionbretagne.png" alt="Région Bretagne">
                </div>
            </article>
        </div>

    </section>

    <!--Contact -->
    <section class="col-md-12 contact background">

        <div class="col-md-10 col-md-offset-1">
            <div class="line"></div>
            <h2 class="text-center section_title">contact</h2>
            <div class="line"></div>
        </div>

        <div class="col-md-10 col-md-offset-1">
            <div class="contact-area">
                <form method="post" action="index.php">
                    <input type="text" name="nom" id="nom" placeholder="Nom" />
                    <input type="text" name="prenom" id="prenom" placeholder="Prénom" />
                    <input type="text" name="email" id="email" placeholder="Email" />
                    <select name="metier" id="metier">
                        <option value='Developpeur'>Developpeur</option>
                        <option value='Designer'>Designer</option>
                        <option value='Chef de projet'>Chef de projet</option>
                        <option value='Marketing / Communication'>Marketing/communication</option>
                    </select>
                    <textarea name="message" rows="20" cols="20" id="message" placeholder="Message"></textarea>
                    <input type="submit" name="submit" value="ENVOYER" class="submit-button"/>
                </form>
            </div>
        </div>
    </section>


    <!--========= Lieux =======-->
    <section class="background background_lieux col-md-12">
        <div class="col-md-10 col-md-offset-1">
            <div class="line"></div>
            <h2 class="text-center section_title">où nous trouver ?</h2>
            <div class="line"></div>
        </div>

        <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d42634.30442059742!2d-1.711119644934551!3d48.09781087327661!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x480ede3708abe89d%3A0x41bb6408008f6136!2sLe+Loft+-+Cowork&#39;in+Rennes!5e0!3m2!1sfr!2sfr!4v1479388776307" width="300" height="550" frameborder="0" style="border: 1px solid black" allowfullscreen></iframe>
    </section>

    <footer>
        <p>HACKATON CODE ACADEMIE - Le 10 -11 Décembre - Au Loft, 3 Rue de Robien, Rennes</p>
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