function parseGame(data)
{
  var rounds = [];

  for (var k = 0; k < data.rounds.length; ++k) {
    // Figure out who is the winner
    var winner = (data.rounds[k].score[0] > data.rounds[k].score[1]) ? 0 : 1;
    var wName = data.match.participants[winner].name;
    var lName = data.match.participants[1 - winner].name;

    var round = {};
    var blob = data.rounds[k].blob;

    for (var i in blob.participants) {
      if (!blob.participants.hasOwnProperty(i)) continue;

      var p = {};
      p.id = blob.participantIdentities[i].player.summonerId;
      p.name = blob.participantIdentities[i].player.summonerName;
      p.items = [
        blob.participants[i].stats.item0,
        blob.participants[i].stats.item1,
        blob.participants[i].stats.item2,
        blob.participants[i].stats.item3,
        blob.participants[i].stats.item4,
        blob.participants[i].stats.item5,
        blob.participants[i].stats.item6
      ];
      p.kills = blob.participants[i].stats.kills;
      p.deaths = blob.participants[i].stats.deaths;
      p.assists = blob.participants[i].stats.assists;
      p.gold = blob.participants[i].stats.goldEarned;
      p.level = blob.participants[i].stats.champLevel;
      p.cs = blob.participants[i].stats.minionsKilled;
      p.lane = blob.participants[i].timeline.lane;

      p.teamId = blob.participants[i].teamId;
      p.championId = blob.participants[i].championId
      p.spells = [
       blob.participants[i].spell1Id,
        blob.participants[i].spell2Id
      ];

      if (!round[p.teamId]) {
        round[p.teamId] = {};
        round[p.teamId].isWinner = blob.participants[i].stats.winner;
        round[p.teamId].participants = [];
        round[p.teamId].name = blob.participants[i].stats.winner ? wName : lName;
      }

      round[p.teamId].participants.push(p);
    }

    rounds.push(round);
  }

  return rounds;
}

function getChamp(key)
{
  for (var i in _champion.data) {
    if (key == _champion.data[i].key) {
      return _champion.data[i].image;
    }
  }
  return null;
}

function getSummoner(key)
{
  for (var i in _summoner.data) {
    if (key == _summoner.data[i].key) {
      return _summoner.data[i].image;
    }
  }
  return null;
}

function getItem(key)
{
  if (_item.data[key]) {
    return _item.data[key].image;
  }

  return null;
}

function formatImg(img)
{
  var h = 36;
  var w = 36;

  if (!img) {
    return '';
  }

  var t = '<img src="/public/img/pix.gif" style="width:'
      + w + 'px;height:'
      + h + 'px;background:url(\'/public/img/sprite/small_'
      + img.sprite + '\') -'
      + Math.round(img.x * w/parseFloat(img.w)) + 'px -'
      + Math.round(img.y * h/parseFloat(img.h)) + 'px" alt=""/>';
  return t;
}

function drawTeam(team, target)
{
  team.participants.sort(function(a, b) {
    var lanes = ['TOP', 'JUNGLE', 'MIDDLE', 'BOTTOM'];
    if (lanes.indexOf(a.lane) != lanes.indexOf(b.lane)) {
      return (lanes.indexOf(a.lane) < lanes.indexOf(b.lane)) ? -1 : 1;
    }

    return (a.cs > b.cs) ? -1 : 1;
  });

  for (var i = 0; i < team.participants.length; ++i) {
    var el = $('champ-template').clone(true);
    el.addClass('player');
    el.getElements('.name')[0].set('text', team.participants[i].name);

    el.getElements('.champ')[0].set('html',
      formatImg(getChamp(team.participants[i].championId)));
    el.getElements('.level')[0].set('text', team.participants[i].level);
    el.getElements('.kda')[0].set('text',
      team.participants[i].kills + '/' +
      team.participants[i].deaths + '/' +
      team.participants[i].assists);

    for (var j = 0; j < team.participants[i].items.length; ++j) {
      el.getElements('.item' + j)[0].set('html',
        formatImg(getItem(team.participants[i].items[j])));
    }

    for (var j = 0; j < team.participants[i].spells.length; ++j) {
      el.getElements('.spell' + j)[0].set('html',
        formatImg(getSummoner(team.participants[i].spells[j])));
    }

    var gold = (Math.round(team.participants[i].gold / 100) / 10) + 'k';
    el.getElements('.gold')[0].set('text', gold);
    el.getElements('.cs')[0].set('text', team.participants[i].cs);

    el.setStyle('display', '');
    el.inject(target);
  }

  $(target).getElements('caption')[0].set('text', team.name);
  if (team.isWinner) {
    var winner = new Element('span');
    winner.className = 'winner';
    winner.set('text', 'Vainqueur');
    winner.inject($(target).getElements('caption')[0], 'top');
  }
}

function loadMatch(id, target)
{
  if (!loadMatch.spinner) {
    loadMatch.spinner = new Spinner(target);
  }

  target.setStyle('display', '');
  loadMatch.spinner.show();

  var req = new Request.JSON({
    url: loadMatch.url.replace(/__id__/g, id),
    onSuccess: function(data) {
      var team = target.getElements('.team');
      target.getElements('h3').set('text', '');

      for (var i = 0; i < 2; ++i) {
        team[i].getElements('h3')[0].set('text', data.match.participants[i].name);
        team[i].getElements('.rank span')[0].set('text', data.match.participants[i].rank);
        team[i].getElements('.won')[0].set('text', data.match.participants[i].won + "W");
        team[i].getElements('.lost')[0].set('text', data.match.participants[i].lost + "L");
      }

      var score = [0, 0];
      for (var i = 0; i < data.rounds.length; ++i) {
        if (data.rounds[i].score[0] > data.rounds[i].score[1]) {
          ++score[0];
        }
        else if (data.rounds[i].score[0] < data.rounds[i].score[1]) {
          ++score[1];
        }
      }

      target.getElements('.score').set('text', score.join("-"));

      var header = target.getElements('header')[0];
      header.removeClass('wleft');
      header.removeClass('wright');
      if (score[0] > score[1]) {
        header.addClass('wleft');
      }
      else if (score[0] < score[1]) {
        header.addClass('wright');
      }

      var rounds = parseGame(data);
      var li = target.getElements('.rounds nav ul li');
      li.set('text', '-');
      li.removeClass('active');
      li.addClass('disabled');

      for (var i = 0; i < data.rounds.length; ++i) {
        li[i].set('text', 'Game ' + (i + 1));
        li[i].removeClass('disabled');
      }

      loadMatch.spinner.hide();
      loadMatch.rounds = rounds;
      loadMatch.data = data;
      loadRound(target, li[0]);
    }
  }).get();
}
loadMatch.spinner = null;
loadMatch.rounds = null;
loadMatch.data = null;


function loadRound(target, li)
{
  if (!loadMatch.rounds) {
    return;
  }

  var rid = parseInt(li.dataset.rid);

  target.getElements('tr.player').each(function(t) { t.dispose(); });
  target.getElements('.winner').dispose();

  drawTeam(loadMatch.rounds[rid][100], 'team100');
  drawTeam(loadMatch.rounds[rid][200], 'team200');

  var dl = target.getElements('.download')[0];
  if (loadMatch.data.rounds[rid].replay) {
    for (var i = 0; i < 5; ++i) {
      dl.removeClass('g' + i);
    }

    dl.addClass('g' + (rid + 1));
    dl.getElements('a')[0].href = loadRound.replayUrl + loadMatch.data.rounds[rid].replay;
    dl.setStyle('visibility', '');
  }
  else {
    dl.setStyle('visibility', 'hidden');
  }

  li.parentNode.getElements('li').removeClass('active');
  li.addClass('active');
}
