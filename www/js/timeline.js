jQuery( document ).ready(function( $ ) {
	var timelineURI = window.bgidomain + "timeline.cgi"

	var monthDataSets = []
	var monthLabels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]

	var dayDataSets = []
	var dayLabels = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]

	var pieDataSets = {}
	var timelineDateIndexed = {}

	/* Retrieve and create the timeline for each month */
	$.get(timelineURI, function(timeline){
		var minTime = new Date().getTime()
		var maxTime = new Date().getTime() + 86400 //add so we get our current results in everytime zone
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
				var itemDate = parseInt(timeline[i].items[j].date)*1000

				/* Create buckets for each date for the timeline*/
				if( itemDate < minTime ) minTime = itemDate
				if(typeof timelineDateIndexed[new Date(itemDate).toDateString()] == "undefined") timelineDateIndexed[new Date(itemDate).toDateString()] = []
				timelineDateIndexed[new Date(itemDate).toDateString()].push(timeline[i].items[j])

				/* Construct data for graphs for month and day */
				monthData[new Date(itemDate).getMonth()] += ( timeline[i].items[j].amount )
				dayData[new Date(itemDate).getDay()] += ( timeline[i].items[j].amount )
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
    	$('canvas').attr('width', $(document).width() - 20)
    	var mctx = document.getElementById("monthcanvas").getContext("2d")
    	var monthchart = new Chart(mctx).Bar(monthData, {})

    	var dayData = {
    		labels: dayLabels,
    		datasets: dayDataSets
    	}
		var dctx = document.getElementById("daycanvas").getContext("2d")
    	var datchart = new Chart(dctx).Bar(dayData, {})

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

    	var timelineByDayCtx = document.getElementById("timelinebyday").getContext("2d")
    	/* So we have an object indexed by date from minDate -> maxDate so now 
    	 * we need to construct the labels for the graph as well as the data 
    	*/
    	var timelineLabels = []
    	var timelineData = []
    	if(minTime < maxTime){ /* Always true. But should check it Just in case. (perhaps bears) */
    		var tmp =  minTime
    		while(tmp < maxTime){
    			var timelineLabelKey = new Date(tmp).toDateString()
    			timelineLabels.push( timelineLabelKey )
    			/* Create the data for the day */
    			if(typeof timelineDateIndexed[timelineLabelKey] == "undefined" || timelineDateIndexed[timelineLabelKey].length == 0) timelineData.push(0)
    			else{
    				var total = 0
    				for(idx in timelineDateIndexed[timelineLabelKey]){
    					total += timelineDateIndexed[timelineLabelKey][idx].amount
    				}
    				timelineData.push(total)
    			}
    			tmp += (86400*1000) /* Not exactly right, but its good enough for now. See http://stackoverflow.com/questions/7552104/is-a-day-always-86-400-epoch-seconds-long */
    		}
    		
    		var timelineChart = new Chart(timelineByDayCtx).Line({
    			labels: timelineLabels,
    			datasets: [
    				{
    					label: "Spent over time (total)",
    					fillColor: "rgba(220,220,220,0.2)",
			            strokeColor: "rgba(220,220,220,1)",
			            pointColor: "rgba(220,220,220,1)",
			            pointStrokeColor: "#fff",
			            pointHighlightFill: "#fff",
			            pointHighlightStroke: "rgba(220,220,220,1)",
			            data: timelineData
    				}
    			]
    		},{})
    	}else{
    		throw "INVALID MIN / MAX TIME FOR TIMELINE DATA"
    	}


    	

	})
})