jQuery( document ).ready(function( $ ) {
	/* Load each account into a different colored patch on the heatmap
     * that way it is easy to discern where and how the money is being
     * spent.
	*/
	var accountsURI =  window.bgidomain + "accounts.cgi"
	var lineItemsURI = window.bgidomain + "list-lineitems.cgi?accountname=" /* + accountName */

	$('#heatmapMap').css({'opacity' : .5})

	var map = L.map('heatmapMap').setView([43.876, -72.081], 8)
	L.tileLayer('http://{s}.tiles.mapbox.com/v3/hyrule10.i5lkp4k8/{z}/{x}/{y}.png', {
    	attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
    	maxZoom: 18
	}).addTo(map);



	$('#heatmapMap').fadeIn()

})