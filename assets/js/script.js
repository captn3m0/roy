jQuery.expr[':'].icontains = function(a, i, m) {
  return jQuery(a).text().toUpperCase()
      .indexOf(m[3].toUpperCase()) >= 0;
};

$.fn.tooltipster('setDefaults', {
  animation: "fade",
  interactive: true,
  multiple: true
});

function parseList(text){
  var mentions = text.match(/(^|\W)@\w+/g) || [];
  for (var i=0; i<mentions.length; i++)
    mentions[i] = mentions[i].replace(/^[^@]/, "").toLowerCase();
  var hashes = text.match(/(#[a-z0-9][a-z0-9\-_]*)/ig) || [];
  for(var j=0;j<hashes.length;j++)
    hashes[j] = hashes[j].replace(/#/g,'').toLowerCase();
  return {
    mentions: mentions,
    hashes: hashes
  }
};

function listHashTags(hashes){
  html = '<a href="#/all">all</a> ';
  for(i in hashes){
    html+='<a href="#/tag/'+hashes[i]+'">#'+hashes[i]+'</a> ';
  }
  $('nav.hashtags').html(html);
}

function listMentions(mentions){
  html = '<a href="#/all">all</a> ';
  for(i in mentions){
    html+='<a href="#/user/'+mentions[i]+'">'+mentions[i]+'</a> ';
  }
  $('nav.mentions').html(html);
}

$(document).ready(function(){
  var mentions = hashes = [];
  var result = parseList($('article ul').text());
  mentions = _.uniq(result.mentions.sort());
  hashes = _.uniq(result.hashes.sort());
  listHashTags(hashes);
  listMentions(mentions);
});

$(window).load(function(){
  $('article li').each(function(i, item){
    var $item = $(item);
    $item.tooltipster({
      content: $('<span><a href="#/done/">done</a></span>'),
      position: "left"
    });
    $item.tooltipster({
      content: $('<span>Created on: '+$item.data('created')+'. <br> Channel: '+$item.data('channel')+' <br> Creator: '+$item.data('creator')+'</span>'),
      position: "top"
    });
  });
});

routie('/all', function(){
  $('article li').show();
});

routie('/tag/:tag', function(tag) {
  $('article li').hide();
  $('article li:icontains('+tag+')').show();
});

routie('/user/:user', function(user){
  $('article li').hide();
  $('article li:contains('+user+')').show();
})