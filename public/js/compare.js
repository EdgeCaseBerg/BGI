var templateNum = 0
window.timeline = []

var monthLabels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]

var dayLabels = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]

var weekPieDataSets = []
var weekPieLables = []

//http://stackoverflow.com/a/6117889/1808164
Date.prototype.getWeekNumber = function(){
    var d = new Date(+this);
    d.setHours(0,0,0);
    d.setDate(d.getDate()+4-(d.getDay()||7));
    return Math.ceil((((d-new Date(d.getFullYear(),0,1))/8.64e7)+1)/7);
};

var monthDataItems = []
var monthPieDataSets = []
for (var i = 0; i < monthLabels.length; i++) {
	monthPieDataSets.push([])	
	monthDataItems.push([])
};

function clearTemplates(){
	$('.compareItem:not(#template):not(#templateWeek)').remove()
}

function showTemplates(){
	$('.compareItem:not(#template):not(#templateWeek').fadeIn()
}


function makeNewTemplate(){
	templateNum++
	cpy = $('#template').clone()
	cpy.attr('id', 'cpy' + templateNum)
	return cpy;
}

function makeNewWeekTemplate(){
	templateNum++
	cpy = $('#templateWeek').clone()
	cpy.attr('id', 'cpy' + templateNum)
	return cpy;
}


function setupMonthData(){
	templateNum = 0;
	clearTemplates()
	for (var i = 0; i < monthLabels.length; i++) {
		var lbl = monthLabels[i]
		tmpl = makeNewTemplate()
		tmpl.find('[name=title]').text(lbl)
		//Use dataset to populate pie and items list
		var pieCanvas = tmpl.find('[name=pie]')[0].getContext("2d")
		var chart = new Chart(pieCanvas).Pie(monthPieDataSets[i], {})
		//populate item list:
		list = tmpl.find('[name=items]')
		var itemsForMonth = monthDataItems[i]
		var total = 0;
		for(idx in itemsForMonth){
			var item = itemsForMonth[idx]
			total += item.amount
			list.append("<li><span>"+item.name+"</span><span class=\"amount\">$"+item.amount.toFixed(2)+"</span></li>")
		}
		tmpl.find('[name=total]').text("Total: $" + total.toFixed(2))
		$('section').append(tmpl)
		tmpl.fadeIn().css('display','')
	};
	showTemplates()
	
}

function setupWeekData(){
	templateNum = 0;
	clearTemplates()
	//compute week by week for each item
	weekPieDataSets// array of arrays containing items per week
	weekPieLables // array of "Week of <>""

	for (var i = weekPieLables.length - 1; i >= 0; i--) {
		var title = weekPieLables[i]
		var data = weekPieDataSets[i]
		var tmpl = makeNewWeekTemplate()
		tmpl.find("[name=title]").text(title)
		//do pie chart in a bit.
		list = tmpl.find('[name=items]')
		var total = 0;
		for(idx in data){
			var item = data[idx]
			total += item.amount
			list.append("<li><span>"+item.name+"</span><span class=\"amount\">$"+item.amount.toFixed(2)+"</span></li>")
		}
		tmpl.find('[name=total]').text("Total: $" + total.toFixed(2))
		if(total != 0){//hack
			/* the above is a hack because right now I'm gettinga single duplicate
			 * template but with no items.. so.. wat?
			*/
			$('section').append(tmpl)
		}
		tmpl.fadeIn().css('display','')
	};

	showTemplates()
}

function setup(){
	var items = []
	for (account in window.timeline) {
		//timeline is by account
		var account = window.timeline[account]
		var color = n2c(account.name)
		var accountTotals = []
		monthLabels.forEach(function(){accountTotals.push(0)})
		for (var i = account.items.length - 1; i >= 0; i--) {
			var item = account.items[i]
			var date = new Date(item.date*1000)
			monthDataItems[date.getMonth()].push(item)
			accountTotals[date.getMonth()] += item.amount
			items.push(item)
		};
		for (var i = accountTotals.length - 1; i >= 0; i--) {
			var accountTotal = accountTotals[i]
			monthPieDataSets[i].push({
				value: accountTotal,
				color: color,
				highlight: n2c(account.name + "a"),
				label: account.name
			})
		};
	};
	//Now deal with weeks:
	items = items.sort(function(l,r){ return l.date == r.date ? 0 : l.date > r.date ? -1 : 1 })
	console.log(items)
	var curWeek = items[0] ? new Date(items[0].date*1000).getWeekNumber() : 0
	var curDateSet = []
	var curLabel = items[0] ? "Week of " + new Date(items[0].date*1000).toLocaleDateString() : ("Week #" + weekKey)
	for (var i = items.length - 1; i >= 0; i--) {
		var item = items[i]
		var weekKey = new Date(item.date*1000).getWeekNumber()
		console.log(weekKey)
		if(curWeek != weekKey || i == 0){
			if(i==0){
				curDateSet.push(item)
			}
			weekPieDataSets.push(curDateSet)
			weekPieLables.push(curLabel)
			curDateSet = []
			curLabel = "Week of " + new Date(item.date*1000).toLocaleDateString()
		}
		curDateSet.push(item)
		curWeek = weekKey
	};

	$('[name=byweek]').click(function(){
		setupWeekData()
	})
	$('[name=bymonth]').click(function(){
		setupMonthData()	
	})
	$('section').on('click','[name="itemcontainer"] button', function(){
		var items = $(this).parent().find('[name="items"]')
		items.slideToggle()
		$(this).text('Toggle Item Display')
	})
	setupMonthData()
}
	
jQuery( document ).ready(function( $ ) {
	var timelineURI = window.bgidomain + "timeline.cgi"
	//use the timeline data since it's in the closest form we want to work with
	$.get(timelineURI, function(timeline){
		window.timeline = timeline
		$('marquee').fadeOut()
		setup()
	}).error(function(evt){
		alert("Could not load your data! Try logging in again")
		console.error(evt)
		
	})
})