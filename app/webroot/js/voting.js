function vote(url, id, val) {
  $.ajax({
    url: url,
    type: 'POST',
    dataType: 'html',
    data: "data%5BBlog%5D%5Bid%5D=" + id + "&data%5BBlog%5D%5Bvote%5D=" + val,
    success: function(raw_data, textStatus, jqXHR){
      var data = jQuery.parseJSON(raw_data);
      if (data.state === "true") {
        $('#VoteGood').html(data.data.Blog.good);
        $('#VoteBad').html(data.data.Blog.bad);
        $('#VoteSum').html(data.data.Blog.sum);
        $('#VoteMedian').html(data.data.Blog.median);
      }
//      if (data.state === "false") {
//        alert(data.message);
//      }
    },
    error: function(){
      alert("Übertragungsfehler");
    }
  });
}

