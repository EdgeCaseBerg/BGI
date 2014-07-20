jQuery( document ).ready(function( $ ) {
	var timelineURI = window.bgidomain + "timeline.cgi"

	var monthDataSets = []
	var monthLabels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]

	var dayDataSets = []
	var dayLabels = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]

	var pieDataSets = {}

	/* Retrieve and create the timeline for each month */
	$.get(timelineURI, function(timeline){
		for (var i = timeline.length - 1; i >= 0; i--) {
			var accountName = timeline[i].name
			
			/* Initialize data counts*/
			var monthData = []
			var dayData = []
			for (var k= monthLabels.length - 1; k >= 0; k--) {
				monthData.push(0)
			};
			for (var k = dayLabels.length - 1; k >= 0; k--) {
				dayData.push(0)
			};
			var label = accountName
			var color = n2c(accountName)

			if(!(accountName in pieDataSets)){
				pieDataSets[accountName] ={
					value: timeline[i].accountBalance,
					color: color,
					highlight: n2c(accountName + "a"),
					label: accountName
				}
			}

			for (var j = 0; j < timeline[i].items.length; j++) {
				monthData[new Date(parseInt(timeline[i].items[j].date)*1000).getMonth()] += ( timeline[i].items[j].amount )
				dayData[new Date(parseInt(timeline[i].items[j].date)*1000).getDay()] += ( timeline[i].items[j].amount )
			};

			monthDataSets.push({
				label: accountName,
				fillColor: color,
				data: monthData
			})
			
			dayDataSets.push({
				label: accountName,
				fillColor: color,
				data: dayData
			})
		}
		var monthData = {
    		labels: monthLabels,
	    	datasets: monthDataSets
    	}
    	$('canvas').attr('width', $(document).width())
    	var mctx = document.getElementById("monthcanvas").getContext("2d")
    	var myLineChart = new Chart(mctx).Bar(monthData, {})

    	var dayData = {
    		labels: dayLabels,
    		datasets: dayDataSets
    	}
		var dctx = document.getElementById("daycanvas").getContext("2d")
    	var myLineChart = new Chart(dctx).Bar(dayData, {})

    	/* Convert pieData into an actual data set */
    	pieDataSet = []
    	for(datum in pieDataSets){
    		pieDataSet.push(pieDataSets[datum])
    	}
    	var pctx = document.getElementById("accountpie").getContext("2d")
    	var pchart = new Chart(pctx).Pie(pieDataSet,{})

    	function makeLegendRow(name, color){
    		return $("<li style='color: "+color+"'>" + name + "</li>")
    	}
    	/* Add the legend next to the pie chart: */
    	$("section[name=charts]").prepend($("<legend><ul></ul></legend>"))
    	for (var j = pieDataSet.length - 1; j >= 0; j--) {
    		var n = pieDataSet[j].label
    		var c =pieDataSet[j].color
    		$("section[name=charts] ul").parent().append(makeLegendRow(n,c))
    	};
    	

	})
})