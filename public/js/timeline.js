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

	
	var datasets = []

	$.get(accountsURI, function(accounts){
		for (var i = accounts.length - 1; i >= 0; i--) {
			var accountName = accounts[i].name
			$.get(lineItemsURI + accounts[i].name, function(lineitems){
				var data = [0,0,0,0,0,0,0,0,0,0,0,0]
				var label = accountName
				var color = n2c(accountName)

				for (var i = 0; i < lineitems.length; i++) {
					data[new Date(parseInt(lineitems[i].date)*1000).getMonth()] += ( lineitems[i].amount )
				};

				datasets.push({
					label: accountName,
					fillColor: color,
					data: data
				})
			})
		}
		var data = {
    		labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
	    	datasets: datasets
    	}
    	console.log(data)
    	var ctx = document.getElementById("timelinecanvas").getContext("2d");
    	var myLineChart = new Chart(ctx).Line(data, {});
	})
})