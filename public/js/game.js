
function User (id, cards) {
    this.id = id;

    this.cards = {};

    this.$element = null;

    this.init = function(cards) {  
        this.setCards(cards);               
        this.setElement();     
    };

    this.isCurrentUser = function() {
        return Game.currentUserId == this.id;
    }

    this.setCards = function(cards) {
        for (var cardName in cards) {
            this.cards[cardName] = new Card(this, cardName, cards[cardName]);
        }

        return this;
    }

    this.create = function (html) {  
        $('#table').append(html);                        
        this.setElement();

        return this;
    };        

    this.remove = function() {
        this.$element.remove();

        return this;
    };

    this.setElement = function() {
        this.$element = $('#user-' + this.id);

        if (!this.$element) {
            return this;
        }

        for (var cardName in this.cards) {
            this.cards[cardName].setElements();                
        }               

        return this;
    };

    this.getCard = function(cardName) {
        if (typeof this.cards[cardName] != 'undefined') {
            return this.cards[cardName];
        }

        return null;
    };

    this.turnCard = function(cardName) {
        var card = this.getCard(cardName);
        card.turn();
    }             

    this.init(cards);        
};

function Card (user, name, opened) {
    this.user = user;

    this.name = name;

    this.opened = opened;

    this.$frontView = null;   

	this.$backView = null;

	this.compressCssProperties = {};
	
	this.decompressCssProperties = {};		
	
    this.init = function() {
        this.setElements();
    };	

    this.setElements = function() {
        if (!this.user.$element) {
            return false;
        }

        this.$frontView = $('#' + user.$element.attr('id') + ' [data-card-name="' + this.name + '"]').eq(0);
		this.$backView = $('#' + user.$element.attr('id') + ' [data-card-name="' + this.name + '"]').eq(1);		

		this.setCompressionCss();
		
		var viewToHide;

		if (this.$frontView.data('card-opened') != this.opened) {
			viewToHide = this.$frontView;
		} else {
			viewToHide = this.$backView;
		}

		viewToHide.css(this.compressCssProperties);
		
        return true;
    };               

	this.setCompressionCss = function() {
		var marginL = this.$frontView.width() / 2 + 'px';
		
		this.compressCssProperties = {
			width: 0,			
			height: 96,
			marginLeft: marginL,
			opacity: 0.3
		};
		
		this.decompressCssProperties = {
			width: 71,
			height: 96,
			marginLeft: 0,
			opacity: 1
		};				

	};

    this.turn = function() {
        if (!this.$frontView || !this.$backView) {
            return false;
        }

        this.opened = !this.opened;  
		
		var t = this;
		var viewToShow = this.opened ? this.$backView : this.$frontView;
		var viewToHide = this.opened ? this.$frontView : this.$backView;
	
        viewToHide.stop().animate(this.compressCssProperties, 200, function() {
            viewToShow.animate(t.decompressCssProperties, 200);			
        });		
				
        return true;
    }; 

    this.afterTurn = function() {
        var data = {
            'card_name': this.name
        };

        $.post(Game.turnUrl, data, function() {}, 'json');            
    };

    this.init();
}

function Game (data) {

    Game.updateUrl = data['update_url'];

    Game.leaveUrl = data['leave_url'];

    Game.turnUrl = data['turn_url'];

    Game.imgUrl = data['img_url'];

    Game.currentUserId = data['current_user_id'];

    this.lastEventId = data['last_event_id'];

    this.users = {};

    this.init = function(data) {
        this.setUsers(data['users']);                       
        this.bindEvents();                        
    }; 

    this.bindEvents = function() {          
        var t = this;

        window.onbeforeunload = function (e) {
            
            $.ajax({
                url: Game.leaveUrl,
                async: false
            });            
        };         

        $('#log-button').click(function(e) {            
            $('#log').load($(this).attr('href'));
            $('#log').show();

            return false;
        });

        $('[data-active-user]').click(function() {
            var userId = $(this).data('active-user');
            var cardName = $(this).data('card-name');                
            var card = t.users[userId].getCard(cardName);

            card.turn(); 
            card.afterTurn();            
        });            
    };

    this.setUsers = function(usersData) {
        var id;
        var cards; 

        for (var key in usersData) {
            id = usersData[key]['id'];
            cards = usersData[key]['cards'];

            this.users[id] = new User(id, cards);
        }

        return this;
    };

    this.addUser = function(user) {
        this.users[user['id']] = user;

        return this;
    };

    this.getUser = function(id) {
        if (typeof this.users[id] != 'undefined') {
            return this.users[id];
        }

        return null;
    }

    this.removeUser = function(user) {
        delete this.users[user['id']];

        return this;
    }


    this.start = function() {
        var t = this;

        window.setInterval(function() {       
            $.post(Game.updateUrl, {'last_event_id': t.lastEventId}, function(response) {
                var changes = response['changes']; 
                var changesNumber = changes.length;
                var event;
                var user;

                if (changesNumber == 0) {
                    return;
                }

                for (var i = 0; i < changesNumber; i++) {
                    event = changes[i];
                    user = t.getUser(event['user']['id']);
					
					if (i == changesNumber - 1) {
						t.lastEventId = event['eventId']; 
					}					

                    if (user && user.isCurrentUser()) {
                        continue;
                    }

                    if (!user) {
                        user = new User(event['user']['id'], event['user']['cards']);
                        user.create(event['html']);
                        t.addUser(user);
                    } else if (event['user']['left']) {
                        t.removeUser(user);
                        user.remove();
                    } else {  
                        user.turnCard(event['card']);
                    }					
                }
                		
            }, 'json');
        }, 2000);            
    }

    this.init(data);
}