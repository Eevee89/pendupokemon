<body>
    <div id="pokemon">
        <p id="title">Pendu</p>
        <img id="pokemon_logo" src="pokemon_logo.png">
    </div>
    <div id="sep" style="height: 10px;"></div>
    <div id="row"></div>
    <div id="guess">
        <p id="errors">Erreurs : 0/10</p>
        <p id="letters">Lettres :</p>
    </div>
    <div id="bottom">
        <p id="replay">Rejouer</p>
        <p id="score">Score : 0</p>
    </div>
    <div id="tiles">
        <?php for($i=0; $i<6; $i++): ?>
            <div class="tileRow">
                <?php for($j=0; $j<4; $j++): ?>
                    <div id="<?=chr(65+4*$i+$j)?>" class="tile used"><p><?=chr(65+4*$i+$j)?></p></div>
                <?php endfor; ?>
            </div>
        <?php endfor; ?>
        <div class="tileRow">
            <div id="Y" class="tile used"><p>Y</p></div>
            <div id="Z" class="tile used"><p>Z</p></div>
            <div id="Space" class="tile used"><p>_</p></div>
        </div>
    </div>
    <img id="pikachu" src="pikachu.png">
</body>