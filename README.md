# RPG Game using PHP and HTML / CSS / JavaScript

## Project Assignment for the [PHP Web Development @ SoftUni](https://softuni.bg/trainings/1470/php-web-development-october-2016)  course

Design and implementation of [RPG Game](https://bg.wikipedia.org/wiki/%D0%A0%D0%BE%D0%BB%D0%B5%D0%B2%D0%B0_%D0%B8%D0%B3%D1%80%D0%B0) using PHP and HTML / CSS / JavaScript by Plamen Markov

Game Url: [http://rpg.hipokrat.net](http://rpg.hipokrat.net)

### Required functionalities

- User registration / login and user profiles.
- When registered users are placed on a map (2D [x:y], 3D [x:y:z] or something else by your choice)
- Depends on your game story, the users start with more than one platforms (kingdoms, planets, houses, whatever the story is about)
- On each platform the user starts with predefined amount of resources (the game should have at least two resources for example gold and food / metal and mineral / etc...)
- Each user’s platform has something the user can evolve for resources. At least two of them should give income per hour of resources (e.g. buildings, one of them is Gold mine, another is Farm, the mine gives income per hour of resource Gold, the Farm gives income per hour of resource Food)
- The income per hour should be added to the platform’s resources on each 2 minutes (1/30 of the income) e.g. if user clicks each second on the web page and has 3000 gold per hour, on each 2 minutes one will receive 100 gold. If the user does not click for example 20 minutes and refreshes the page should receive 20/2 * 100 = 1000 gold. If the user does not click for example 3 hours and refreshes the page will receive 3 * 3000 = 9000 gold. 
- When building is started to build, it should take some time (each level takes more time). The time should be visualized with a countdown timer. When the building is ready it should take effect.
- On each platform the user can build different army units (e.g. cavalry, fleet, seaships, etc…). Each unit should have dependency on certain building levels and combination of them (e.g. unit X needs 3rd level of dockyard and 8th level of Gas mine)
- Each unit should cost different amount of resources
- User is able to input how much units of each type wants to build
- When units are in building process they take time (e.g. unit X takes 3 mins per unit and unit Y takes 8 mins per unit, user builds 10xUnitX and 8xUnitY, after 30 mins all the UnitX will be ready and UnitY will have 34 minutes’ time remaining; or you may implement it on each 3 mins one X is ready and on each 8 mins one Y is ready, it was just a hint)
- User is able to attack another user with its units. The user choses how many of each unit type to send to another user. Each army journey from one player’s platform to another player’s platform takes time depending on the coordinate distance (e.g. from [2:12] to [8:6]). Both users can see informing message in their homepage. The aggressor sees who is attacking and the victim seems who attacks him. Both see the time remaining until the impact.
- When the army journey reaches the hostile platform a battle happens. Each unit should have some kind of statistics in order to make a battle e.g. UnitX is weaker than UnitY so 200xUnitX against 100xUnitY results in loss of UnitX and UnitY remains with 40.
- There is a battle report visible for both sides with the result of the battle. Who won, who loss or if the battle is draw. How many units both sides left with after the battle.
- If there are left units of the attacker, a backwards journey is made so the army is coming home
- When the backwards journey ends, the army gets down on the users platform

### Utils

- `https://github.com/natanfelles/codeigniter-phpstorm` - Enable PhpStorm Code Completion to CodeIgniter




