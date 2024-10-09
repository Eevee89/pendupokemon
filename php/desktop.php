<body>
    <div id="pokemon">
        <p id="title">Pendu</p>
        <img id="pokemon_logo" src="pokemon_logo.png">
    </div>
    <div id="generations">
        <p id="gens">Générations actives : </p>
        <?php for($i=1; $i<=9; $i++): ?>
            <div id=<?="$i"?> class="gen success"><p style="pointer-events: none;"><?=$i?></p></div>
        <?php endfor ?>
    </div>
    <div id="row"></div>
    <div id="bottom">
        <p id="replay">Rejouer</p>
        <p id="score">Score : 0</p>
    </div>
    <div id="guess">
        <p id="errors">Erreurs : 0/10</p>
        <p id="letters">Lettres :</p>
    </div>
    <img id="answer" src="https://www.pokebip.com/pokedex-images/300/1.png?v=ev-blueberry">
    <img id="pikachu" src="pikachu.png">
</body>