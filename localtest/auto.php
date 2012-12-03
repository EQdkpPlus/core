

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
                    "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <script src="jquery-1.2.3.min.js"></script>
  <script src="jquery.ui.autocomplete.js"></script>
  <script src="jquery.dimensions.js"></script>

  <script>
  $(document).ready(function(){
    $("input.serverlist").autocomplete({ list: ["Aegwynn","Aerie Peak","Agamaggan","Aggramar","Ahn'Qiraj","Al'Akir","Alexstrasza","Alleria","Alonsus","Aman'Thul","Anachronos","Anetheron","Antonidas","Anub'arak","Arak-arahm","Arathi","Arathor","Archimonde","Argent Dawn","Arygos","Aszune","Auchindoun","Azjol-Nerub","Azshara","Baelgun","Balnazzar","Blackhand","Blackmoore","Blackrock","Bladefist","Bloodfeather","Bloodscalp","Boulderfist","Bronze Dragonflight","Bronzebeard","Burning Blade","Burning Legion","Burning Steppes","C'Thun","Cho'gall","Chromaggus","Confrérie du Thorium","Conseil des Ombres","Crushridge","Culte de la Rive noire","Daggerspine","Dalaran","Dalvengyr","Darkmoon Faire","Darksorrow","Darkspear","Das Syndikat","Deathwing","Defias Brotherhood","Dentarg","Der Mithrilorden","Der Rat von Dalaran","Der abyssische Rat","Destromath","Dethecus","Die Arguswacht","Die Nachtwache","Die Silberne Hand","Die Todeskrallen","Die ewige Wacht","Doomhammer","Draenor","Dragonblight","Drak'thul","Drek'Thar","Dun Modr","Dun Morogh","Dunemaul","Durotan","Earthen Ring","Eitrigg","Eldre'Thalas","Elune","Emerald Dream","Emeriss","Eonar","Eredar","Executus","Festung der Stürme","Forscherliga","Frostmourne","Frostwhisper","Frostwolf","Garona","Genjuros","Ghostlands","Gilneas","Gorgonnash","Grim Batol","Gul'dan","Hakkar","Haomarush","Hellfire","Hellscream","Hyjal","Illidan","Jaedenar","Kael'thas","Karazhan","Kargath","Kazzak","Kel'Thuzad","Khadgar","Khaz Modan","Khaz'goroth","Kil'Jaeden","Kilrogg","Kirin Tor","Kor'gall","Krag'jin","Krasus","Kul Tiras","Kult der Verdammten","La Croisade écarlate","Laughing Skull","Les Sentinelles","Lightbringer","Lightning's Blade","Los Errantes","Lothar","Madmortem","Magtheridon","Mal'Ganis","Malfurion","Malygos","Mannoroth","Mazrigos","Medivh","Minahonda","Molten Core","Moonglade","Mug'thol","Nagrand","Nathrezim","Nazjatar","Nefarian","Ner'zhul","Nera'thor","Nordrassil","Norgannon","Nozdormu","Onyxia","Outland","Perenolde","Proudmoore","Quel'Thalas","Ragnaros","Rajaxx","Rashgarroth","Ravencrest","Ravenholdt","Rexxar","Runetotem","Sargeras","Scarshield Legion","Sen'jin","Bloodhoof","Neptulon","Steamwheedle Cartel","Uldum","Shadowmoon","Shadowsong","Shattered Halls","Shattered Hand","Shen'dralar","Silvermoon","Sinstralis","Spinebreaker","Sporeggar","Stonemaul","Stormrage","Stormreaver","Stormscale","Sunstrider","Suramar","Sylvanas","Taerar","Talnivarr","Tarren Mill","Teldrassil","Terenas","Terrordar","The Maelstrom","The Sha'tar","The Venture Co.","Theradras","Thrall","Throk'Feroth","Aerie Peak","Agamaggan","Aggramar","Akama","Alexstrasza","Alleria","Altar of Storms","Alterac Mountains","Aman'Thul","Anetheron","Antonidas","Anub'arak","Anvilmar","Arathor","Archimonde","Area 52","Argent Dawn","Arthas","Arygos","Auchindoun","Azgalor","Azjol-Nerub","Azshara","Azuremyst","Baelgun","Balnazzar","Barthilas","Black Dragonflight","Blackhand","Blackrock","Blackwater Raiders","Blackwing Lair","Blade's Edge","Bladefist","Bleeding Hollow","Blood Furnace","Bloodhoof","Bloodscalp","Bonechewer","Boulderfist","Bronzebeard","Burning Blade","Burning Legion","Cenarion Circle","Cenarius","Cho'gall","Chromaggus","Coilfang","Crushridge","Daggerspine","Dalaran","Dalvengyr","Dark Iron","Darrowmere","Dath'Remar","Deathwing","Demon Soul","Dentarg","Destromath","Dethecus","Detheroc","Doomhammer","Draenor","Dragonblight","Dragonmaw","Drak'thul","Draka","Drenden","Dunemaul","Durotan","Duskwood","Earthen Ring","Echo Isles","Eitrigg","Eldre'Thalas","Emerald Dream","Eonar","Eredar","Executus","Exodar","Farstriders","Feathermoon","Fenris","Firetree","Frostmane","Frostmourne","Frostwolf","Garithos","Garona","Gilneas","Gorefiend","Gorgonnash","Greymane","Gul'dan","Gurubashi","Haomarush","Hellscream","Hydraxis","Hyjal","Icecrown","Illidan","Jaedenar","Jubei'Thos","Kael'thas","Kalecgos","Kargath","Kel'Thuzad","Khadgar","Khaz'goroth","Kil'Jaeden","Kilrogg","Kirin Tor","Korgath","Kul Tiras","Laughing Skull","Lethon","Lightbringer","Lightning's Blade","Lightninghoof","Llane","Lothar","Madoran","Maelstrom","Magtheridon","Maiev","Mal'Ganis","Malfurion","Malorne","Malygos","Mannoroth","Medivh","Misha","Mok'Nathal","Moon Guard","Moonrunner","Muradin","Nagrand","Nathrezim","Nazjatar","Ner'zhul","Nordrassil","Norgannon","Onyxia","Perenolde","Proudmoore","Quel'dorei","Ravencrest","Ravenholdt","Rexxar","Rivendare","Sargeras","Scarlet Crusade","Scilla","Sen'jin","Sentinels","Shadow Council","Shadowmoon","Shandris","Shattered Halls","Shattered Hand","Shu'Halo","Silver Hand","Silvermoon","Sisters of Elune","Skullcrusher","Skywall","Smolderthorn","Spinebreaker","Staghelm","Steamwheedle Cartel","Stonemaul","Stormrage","Stormreaver","Stormscale","Suramar","Tanaris","Terenas","Terokkar","Thaurissan","The Forgotten Coast","The Scryers","The Underbog","Thorium Brotherhood","Thrall","Thunderhorn","Thunderlord","Tichondrius","Tortheldrin","Trollbane","Turalyon","Uldaman","Uldum","Ursin","Uther","Vashj","Vek'nilash","Velen","Warsong","Whisperwind","Wildhammer","Windrunner","Ysera","Ysondre","Zangarmarsh","Zul'jin","Zuluhed"]})

  });
  </script>

   <style>
                  ul.jq-ui-autocomplete {
                    position: absolute;
                    overflow: hidden;
                    background-color: #fff;
                    border: 1px solid #aaa;
                    margin: 0px;
                    padding: 0;
                    list-style: none;
                    font: Verdana, Arial, sans-serif;
                    color: #333;
                    z-index: 2000;
                  }
                  ul.jq-ui-autocomplete li {
                    display: block;
                    padding: .3em .5em .3em .3em;
                    overflow: hidden;
                    width: 100%;
                  }

                  ul.jq-ui-autocomplete li.active {
                    background-color: #3875d7;
                    color: #fff;
                  }
                </style>


</head>
<body>

<input name="test" value="rel" class="serverlist"/>


</body>
</html>





