<?php

$classes = "";
if (isset($_SESSION["Username"])) {
    $classes = "connected";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST["FORMTYPE"] === "SIGNINFORM") {
        $hash = $dbservice->getPassword($_POST["username"]);
        if (password_verify($_POST["password"], $hash)) {
            $_SESSION["Username"] = $_POST["username"];
            $classes = "connected";
            echo '<script>alert("Ravi de vous revoir '.$_SESSION["Username"].' .")</script>';
        }
        else {
            echo '<script>alert("Le nom d\'utilisateur ou mot de passe est erroné.")</script>';
        }
    }
    else if ($_POST["FORMTYPE"] === "SIGNUPFORM") {
        if ($_POST["password"] === $_POST["cpassword"] ) {
            if ($dbservice->createAccount($_POST["username"], password_hash($_POST["password"], PASSWORD_BCRYPT))) {
                $_SESSION["Username"] = $_POST["username"];
                $classes = "connected";
                echo '<script>alert("Vous êtes inscrit, bienvenue.")</script>';
            }
            else {
                echo '<script>alert("Une erreur est survenue\nLe comtpe n\' pas été créé.")</script>';
            }
        }
        else {
            echo '<script>alert("La confirmation n\'est pas égale au mot de passe entré.")</script>';
        }
    }
    else if ($_POST["FORMTYPE"] === "CHANGEPASS") {
        if ($_POST["password"] === $_POST["cpassword"] ) {
            if ($dbservice->modifyPassword($_SESSION["Username"], password_hash($_POST["password"], PASSWORD_BCRYPT))) {
                echo '<script>alert("Mot de passe modifié avec succès.")</script>';
            }
            else {
                echo '<script>alert("Une erreur est survenue\nMot de passe non modifié.")</script>';
            }
        }
        else {
            echo '<script>alert("La confirmation n\'est pas égale au mot de passe entré.")</script>';
        }
        $classes = "connected";
    }
    else {
        unset($_SESSION["Username"]);
        $classes = "";
    }

}
?>

<body>
    <div id="scoreList">
        <img src="images/menu_mobile.png" srcset="images/menu_mobile.svg">
    </div>
    <?php if(!$isios): ?>
        <div id="dlapk">
            <a href="https://jorismartin.fr/apk/pendupokemon.apk" download="pendupokemon.apk">
                <img src="images/download_mobile.png" srcset="images/download_mobile.svg">
            </a>
            <p>Télécharger l'APK</p>
        </div>
    <?php endif; ?>
    <div id="connect" class=<?=$classes?>>
        <?php if(isset($_SESSION["Username"])): ?>
            <p id="myaccount">Mon compte</p>
            <p id="session_user" hidden>Bienvenue <?=$_SESSION["Username"]?></p>
        <?php else: ?>
            <p id="signup">S'inscrire</p>
            <p id="signin">Se connecter</p>
        <?php endif; ?>
    </div>
    <div id="pokemon">
        <p id="title">Pendu</p>
        <img id="pokemon_logo" src="images/pokemon_logo.png">
    </div>
    <div id="sep" style="height: 10px;"></div>
    <div id="generations">
        <p id="gens">Générations actives : </p>
        <?php for($i=1; $i<=9; $i++): ?>
            <div id=<?="$i"?> class="gen success"><p style="pointer-events: none;"><?=$i?></p></div>
        <?php endfor ?>
    </div>
    <div id="sep" style="height: 10px;"></div>
    <div id="row"></div>
    <div id="hints" hidden>
        <img id="type1" src="">
        <img id="type2" hidden src="">
    </div>
    <div id="guess">
        <p id="errors">Erreurs : 0/10</p>
        <p id="letters">Lettres :</p>
    </div>
    <div id="bottom">
        <p id="replay">Rejouer</p>
        <p id="hint">Indice</p>
        <p id="score">Score : 0</p>
    </div>
    <img id="answer" src="https://www.pokebip.com/pokedex-images/300/1.png?v=ev-blueberry" hidden>
    <div id="tiles">
        <?php for($i=0; $i<6; $i++): ?>
            <div class="tileRow">
                <?php for($j=0; $j<4; $j++): ?>
                    <div id="<?=chr(65+4*$i+$j)?>" class="tile used"><p class="tileP"><?=chr(65+4*$i+$j)?></p></div>
                <?php endfor; ?>
            </div>
        <?php endfor; ?>
        <div class="tileRow">
            <div id="Y" class="tile used"><p>Y</p></div>
            <div id="Z" class="tile used"><p>Z</p></div>
            <div id="Space" class="tile used"><p>_</p></div>
        </div>
    </div>
    <img id="pikachu" src="images/pikachu.png">

    <div id="scoreModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div style="display: flex; justify-content: center;">
                <table id="scoreTable">
                    <caption>Liste des scores</caption>
                    <thead>
                        <tr>
                            <th scope="col">Pseudo</th>
                            <th scope="col">Meilleur score</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="signupModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form action="" method="POST">
                <input name="FORMTYPE" type="hidden" value="SIGNUPFORM">
                <input name="username" id="suusername" type="text" placeholder="Entrez votre pseudo" maxlength="15">
                <input name="password" id="supassword" type="password" placeholder="Entrez votre mot de passe" maxlength="15">
                <input name="cpassword" id="sucpassword" type="password" placeholder="Confirmez votre mot de passe" maxlength="15">
                <div style="display: flex; justify-content: center; width: 100%;">
                    <input id="signupBtn" type="submit">
                </div>
            </form>
        </div>
    </div>

    <div id="signinModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form action="" method="POST">
                <input name="FORMTYPE" type="hidden" value="SIGNINFORM">
                <input name="username" id="siusername" type="text" placeholder="Entrez votre pseudo" maxlength="15">
                <input name="password" id="sipassword" type="password" placeholder="Entrez votre mot de passe" maxlength="15">
                <div style="display: flex; justify-content: center; width: 100%;">
                    <input id="signinBtn" type="submit">
                </div>
            </form>
        </div>
    </div>

    <div id="accountModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form action="" method="POST">
                <input name="FORMTYPE" type="hidden" value="CHANGEPASS">
                <input name="password" id="chpassword" type="password" placeholder="Entrez nouv. mot de passe" maxlength="15">
                <input name="cpassword" id="chcpassword" type="password" placeholder="Confirmez nouv. mot de passe" maxlength="15">
                <div style="display: flex; justify-content: center; width: 100%;">
                    <input id="changeBtn" type="submit" value="Changer le mdp">
                </div>
            </form>
            <form action="" method="POST">
                <input name="FORMTYPE" type="hidden" value="DISCONNECTION">
                <div style="display: flex; justify-content: center; width: 100%;">
                    <input id="discoBtn" type="submit" class="close" value="Déconnexion">
                </div>
            </form>
        </div>
    </div>
</body>