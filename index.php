<html>
    <head>
    </head>
    <body>
	<span id="game-state"></span>
    </body>
</html>

<script>
 // Game states: NOT_STARTED, DRIVING, INTERRUPTED, ENDED
 
 const game_state_span = document.getElementById('game-state');
 
 const game = {
     state: 0,
     interrupt_queue: [],
     startup_s: 3,
     startAnimation: function(secs) {
	 for (let count=secs; count>0; --count){
	     setTimeout(() => { game_state_span.innerText = count; }, (secs-count)*1000);
	 }
     },
     startGame: function() {
	 this.startAnimation(this.startup_s);
	 setTimeout(() => { game_state_span.innerText = 'GAME ON'; }, (this.startup_s*1000));
     }
 }

 game.startGame();
</script>
