$(function()
{
	$('#head_crawl_result').hide();
	$('#btn_crawl').click(function()
	{
		$('#prog_crawling').show();
		$('#btn_crawl').hide();
		$('#head_crawl_result').hide();
		$.ajax(
		{
			url: 'server_side/crawler.php?scans='+$("#in_scans").val()+'&url='+encodeURIComponent($("#in_url").val()),
			success: function(searchResultsJSON)
			{
				$('#prog_crawling').hide();
				$('#btn_crawl').show();
				$('#head_crawl_result').show();
			}
		});
	})
});