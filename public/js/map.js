jQuery( document ).ready(function( $ ) {
	/* Load each account into a different colored patch on the heatmap
     * that way it is easy to discern where and how the money is being
     * spent.
	*/
	
	var accountsURI =  window.bgidomain + "accounts.cgi"
	var lineItemsURI = window.bgidomain + "list-lineitems.cgi?accountname=" /* + accountName */

	function n2c(name){
		var colors = ['red','blue','yellow','green','purple']
		var color = 1;
		for(idx in name){
			color += name.charCodeAt(idx)*idx
		}
		return colors[color % colors.length];
	}

	function roundLoc(latitudeOrLongitude){
		return latitudeOrLongitude.toFixed(4)//we dont need to be SUPER precise, after all it's an aggregate
	}

	if(typeof L == "undefined"){
		window.console.error("Leaflet Library Could not be loaded")
		return
	}

	var map = L.map('themap').setView([43.876, -72.081], 8)
	L.tileLayer('http://{s}.tiles.mapbox.com/v3/hyrule10.i5lkp4k8/{z}/{x}/{y}.png', {
    	attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
    	maxZoom: 18
	}).addTo(map);

	$.get(accountsURI, function(accounts){
		for (var i = accounts.length - 1; i >= 0; i--) {
			var accountName = accounts[i].name
			$.get(lineItemsURI + accounts[i].name, function(lineitems){
				var items = {}
				for (var i = lineitems.length - 1; i >= 0; i--) {
					var item = lineitems[i]
					item.latitude = roundLoc(item.latitude)
					item.longitude = roundLoc(item.longitude)
					if( items[item.latitude] ){
						if(items[item.latitude][item.longitude]){
							items[item.latitude][item.longitude]["amt"] += item.amount
							items[item.latitude][item.longitude]["cnt"] += 1 
						}else{
							items[item.latitude][item.longitude] = {"amt" : item.amount, "cnt" : 1}
						}
					}else{
						items[item.latitude] = {}
						items[item.latitude][item.longitude] = {"amt" : item.amount, "cnt" : 1}
					}
				};

				/* Now we have an object keyed by latitude, who holds objects keyed by longitude, 
				 * that hold the aggregate sum of their amounts and how many there are.
				*/
				for(latitude in items){
					for(longitude in items[latitude] ){
						var item = items[latitude][longitude]
						var circle = L.circle([latitude, longitude], 500, {
		    				color: n2c(accountName),
		    				fillColor: '#f03',
		    				fillOpacity: 0.5
						}).addTo(map);
						circle.bindPopup("<div>" + item.amt+ "Spent<br/>" + item.cnt  + " Items<br/>"+(item.amt/item.cnt) +" Avg.</div>");
					}
				}
				$('#themap').fadeIn()
			})
		};
		$('#themap').fadeIn()
	})

	

})