<html>
    <head>
    </head>

    <style>
     .coverAll {
	 position: absolute;
	 top: 0;
	 left: 0;
	 width: 100vw;
	 height: 100vh;
	 justify-content: center;
	 align-items: center;
	 background: white;
	 display: none;
     }
    </style>
    
    <body>
	<span id="game-state"></span>
	<br><span id="tape-deck"></span>
	<br><span id="engine-light">ALL GOOD</span>
	<div id="engine-bay" class="coverAll">ENGINE BAY<br>> FIX <</div>
	<div id="game-over" class="coverAll">GAME OVER</div>
    </body>
</html>

<script>
 // Game states: S: Stopped, D: Driving, I: Cabin interruption ongoing, E: Repairing engine
 
 const game_state_span = document.getElementById('game-state');
 const tape_deck = document.getElementById('tape-deck');
 const engine_light = document.getElementById('engine-light');
 const engine_bay = document.getElementById('engine-bay');
 const game_over = document.getElementById('game-over');
 
 const game = {
     // Game initial settings
     STARTUP_S: 1,
     INTERRUPT_S: 0.5,
     INTERRUPT_CHANCE: 0.1,
     INTERRUPTS: [
	 {
	     "text": "This song STINKS",
	     "init": function(){
		 game.state = 'I';
		 countdown = setTimeout(game.endGame, 1000, "Pilot too angry to drive");
		 tape_deck.innerText = 'err click me to change the song..';
		 tape_deck.addEventListener('click', function(){
		     clearTimeout(countdown);
		     tape_deck.innerText = '';
		     game.state = 'D';
		 });
	     },
	     "active": function(){}
	 },
	 {
	     "text": "There's something oily wrong with the engine...",
	     "init": function(){
		 game.state = 'I';
		 countdown = setTimeout(game.endGame, 1000, "All the oil leaked out - She's a goner");
		 engine_light.innerText = 'ENGINE PROBLEM';
		 engine_light.addEventListener('click', function(){
		     engine_bay.style.display = 'flex';
		     engine_bay.addEventListener('click', function(){
			 clearTimeout(countdown);
			 engine_light.innerText = 'ALL GOOD';
			 engine_bay.style.display = 'none';
			 game.state = 'D';
		     });
		 });
	     },
	     "active": function(){}
	 }
     ],

     // Live game variables
     state: 'S',
     interrupt: null,
     interrupt_queue: [],
     
     startAnimation: function(secs){
	 for (let count=secs; count>0; --count){
	     setTimeout(() => { game_state_span.innerText = count; }, (secs-count)*1000);
	 }
     },
     potentialInterruption: function(){ // Random chance to start/queue a new interruption
	 if (game.INTERRUPT_CHANCE > Math.random()){
	     const this_interrupt = game.INTERRUPTS[Math.floor(Math.random() * game.INTERRUPTS.length)]; // Pick a random interrupt to happen
	     this_interrupt["init"](); // Start the interrupt's init (change graphics if relevant)
	     game.interrupt_queue.push(this_interrupt); // Add the interrupt to the queue
	 }
     },
     begin: function(){
	 this.startAnimation(this.STARTUP_S);
	 setTimeout(() => { game_state_span.innerText = 'GAME ON'; this.state = 'D'; }, (this.STARTUP_S*1000));
	 this.interrupt = setInterval(this.potentialInterruption, (this.INTERRUPT_S*1000)); // Bind makes 'this' still refer to the object context
     },
     endGame: function(text=null){
	 if (text != null){
	     game_over.innerText = text;
	 }
	 game_over.style.display = 'flex';
     }
 };

 game.begin();
</script>
