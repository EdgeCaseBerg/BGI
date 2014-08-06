var templateNum = 0
var timelineURI = window.bgidomain + "timeline.cgi"
window.timeline = []

var monthDataSets = []
var monthLabels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]

var dayDataSets = []
var dayLabels = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]

var pieDataSets = {}
var timelineDateIndexed = {}


function makeNewTemplate(){
	templateNum++
	cpy = $('#template').clone()
	cpy.attr('id', 'cpy' + templateNum)
	return cpy;
}

function setupMonthData(){
	templateNum = 0;
	$('compareItem:not(#template)').remove()
	for (var i = 0; i < monthLabels.length; i++) {
		var lbl = monthLabels[i]
		tmpl = makeNewTemplate()
		tmpl.find('[name=title]').text(lbl)
		//Use dataset to populate pie and items list
		tmpl.fadeIn().css('display','')

		$('section').append(tmpl)
	};
	$('compareItem:not(#template)').fadeIn()
}

function setup(){
	$('name[byweek]').click(function(){
		
		console.log(window.timeline)
	})
	$('name[bymonth]').click(function(){
		setupMonthData()	
	})
	$('section').on('click','[name="itemcontainer"] button', function(){
		var items = $(this).parent().find('[name="items"]')
		items.slideToggle()
		$(this).text('Toggle Item Display')

	})
}
	
jQuery( document ).ready(function( $ ) {
	//use the timeline data since it's in the closest form we want to work with
	
setup()
	$.get(timelineURI, function(timeline){
		window.timeline = timeline
		setup()
	}).error(function(evt){
		console.error(evt)
		//throw "Could not load data for comparisons!"
	})
})