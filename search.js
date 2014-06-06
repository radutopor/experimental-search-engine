$(function()
{
	$('#head_search_results').hide();
	$('#btn_search').click(function()
	{
		$('#search_results').empty();
		$('#prog_searching').show();
		$('#btn_search').hide();
		$('#head_search_results').hide();
		$.ajax(
		{
			url: 'server_side/search.php?keyword=' + $("#in_search").val(),
			success: function(searchResultsJSON)
			{
				$('#prog_searching').hide();
				$('#btn_search').show();
				$('#head_search_results').show();
				
				searchResults = $.parseJSON(searchResultsJSON);
				
				$('#head_search_results').text(searchResults.length == 0 ? "No results found." : "Search results");
				
				for (i = 0; i < searchResults.length; i++)
				{
					searchResult = $('<h3><a href="'+searchResults[i]+'" target="_blank">'+searchResults[i]+'</a></h3>');
					$('#search_results').append(searchResult);
				}
			}
		});
	})
});