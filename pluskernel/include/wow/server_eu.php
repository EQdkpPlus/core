<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2007
 * Date:        $Date:  $
 * -----------------------------------------------------------------------
 * @author      $Author:  $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 516 $
 * 
 * $Id:  $
 */
 
if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

    $se = array();
// German Realms
    $se[] = "Aegwynn";
    $se[] = "Alexstrasza"; 
    $se[] = "Alleria";
    $se[] = "Aman'Thul";
    $se[] = "Ambossar";
    $se[] = "Anetheron";
    $se[] = "Antonidas";
    $se[] = "Anub'arak";
    $se[] = "Area 52";
    $se[] = "Arthas";
    $se[] = "Arygos";
    $se[] = "Azshara";
    $se[] = "Baelgun";
    $se[] = "Blackhand";
    $se[] = "Blackmoore";
    $se[] = "Blackrock";
    $se[] = "Blutkessel"; 
    $se[] = "Dalvengyr"; 
    $se[] = "Das Konsortium"; 
    $se[] = "Das Syndikat"; 
    $se[] = "Der abyssische Rat";
    $se[] = "Der Mithrilorden";
    $se[] = "Der Rat von Dalaran"; 
    $se[] = "Destromath";
    $se[] = "Dethecus";
    $se[] = "Die Aldor";
    $se[] = "Die Arguswacht";
    $se[] = "Die ewige Wacht";
    $se[] = "Die Nachtwache"; 
    $se[] = "Die Silberne Hand"; 
    $se[] = "Die Todeskrallen";
    $se[] = "Dun Morogh"; 
    $se[] = "Durotan"; 
    $se[] = "Echsenkessel"; 
    $se[] = "Eredar"; 
    $se[] = "Festung der Stürme"; 
    $se[] = "Forscherliga"; 
    $se[] = "Frostmourne"; 
    $se[] = "Frostwolf";
    $se[] = "Garrosh"; 
    $se[] = "Gilneas"; 
    $se[] = "Gorgonnash"; 
    $se[] = "Gul'dan"; 
    $se[] = "Kargath"; 
    $se[] = "Kel'Thuzad"; 
    $se[] = "Khaz'goroth"; 
    $se[] = "Kil'Jaeden"; 
    $se[] = "Krag'jin"; 
    $se[] = "Kult der Verdammten"; 
    $se[] = "Lordaeron"; 
    $se[] = "Lothar"; 
    $se[] = "Madmortem"; 
    $se[] = "Mal'Ganis"; 
    $se[] = "Malfurion"; 
    $se[] = "Malorne";
    $se[] = "Malygos"; 
    $se[] = "Mannoroth";
    $se[] = "Mug'thol"; 
    $se[] = "Nathrezim"; 
    $se[] = "Nazjatar"; 
    $se[] = "Nefarian"; 
    $se[] = "Nera'thor"; 
    $se[] = "Nethersturm";
    $se[] = "Norgannon"; 
    $se[] = "Nozdormu"; 
    $se[] = "Onyxia"; 
    $se[] = "Perenolde"; 
    $se[] = "Proudmoore"; 
    $se[] = "Rajaxx";
    $se[] = "Rexxar"; 
    $se[] = "Sen'jin"; 
    $se[] = "Shattrath"; 
    $se[] = "Taerar"; 
    $se[] = "Teldrassil"; 
    $se[] = "Terrordar"; 
    $se[] = "Theradras"; 
    $se[] = "Thrall"; 
    $se[] = "Tichondrius"; 
    $se[] = "Tirion"; 
    $se[] = "Todeswache"; 
    $se[] = "Un'Goro"; 
    $se[] = "Vek'lor";
    $se[] = "Wrathbringer"; 
    $se[] = "Ysera"; 
    $se[] = "Zirkel des Cenarius"; 
    $se[] = "Zuluhed"; 

// English Realms    
    $se[] = "Aerie Peak"; 
    $se[] = "Agamaggan"; 
    $se[] = "Aggramar";
    $se[] = "Ahn'Qiraj";
    $se[] = "Al'Akir";
    $se[] = "Alonsus"; 
    $se[] = "Anachronos"; 
    $se[] = "Arathor";
    $se[] = "Argent Dawn";
    $se[] = "Aszune"; 
    $se[] = "Auchindoun"; 
    $se[] = "Azjol-Nerub"; 
    $se[] = "Azuremyst";
    $se[] = "Balnazzar"; 
    $se[] = "Blade's Edge";
    $se[] = "Bladefist"; 
    $se[] = "Bloodfeather"; 
    $se[] = "Bloodhoof"; 
    $se[] = "Bloodscalp"; 
    $se[] = "Boulderfist"; 
    $se[] = "Bronze Dragonflight"; 
    $se[] = "Bronzebeard"; 
    $se[] = "Burning Blade";
    $se[] = "Burning Legion";
    $se[] = "Burning Steppes"; 
    $se[] = "Chamber of Aspects"; 
    $se[] = "Chromaggus"; 
    $se[] = "Crushridge";
    $se[] = "Daggerspine"; 
    $se[] = "Darkmoon Faire"; 
    $se[] = "Darksorrow"; 
    $se[] = "Darkspear"; 
    $se[] = "Deathwing"; 
    $se[] = "Defias Brotherhood"; 
    $se[] = "Dentarg"; 
    $se[] = "Doomhammer";
    $se[] = "Draenor";
    $se[] = "Dragonblight"; 
    $se[] = "Dragonmaw"; 
    $se[] = "Drak'thul";
    $se[] = "Dunemaul"; 
    $se[] = "Earthen Ring"; 
    $se[] = "Emerald Dream";
    $se[] = "Emeriss"; 
    $se[] = "Eonar"; 
    $se[] = "Executus"; 
    $se[] = "Frostmane";
    $se[] = "Frostwhisper"; 
    $se[] = "Genjuros"; 
    $se[] = "Ghostlands";
    $se[] = "Grim Batol";
    $se[] = "Hakkar"; 
    $se[] = "Haomarush"; 
    $se[] = "Hellfire"; 
    $se[] = "Hellscream";
    $se[] = "Jaedenar"; 
    $se[] = "Karazhan"; 
    $se[] = "Kazzak";
    $se[] = "Khadgar"; 
    $se[] = "Kilrogg"; 
    $se[] = "Kor'gall"; 
    $se[] = "Kul Tiras"; 
    $se[] = "Laughing Skull"; 
    $se[] = "Lightbringer"; 
    $se[] = "Lightning's Blade"; 
    $se[] = "Magtheridon"; 
    $se[] = "Mazrigos"; 
    $se[] = "Moonglade"; 
    $se[] = "Nagrand"; 
    $se[] = "Neptulon";
    $se[] = "Nordrassil";
    $se[] = "Outland";
    $se[] = "Quel'Thalas"; 
    $se[] = "Ragnaros"; 
    $se[] = "Ravencrest";
    $se[] = "Ravenholdt"; 
    $se[] = "Runetotem"; 
    $se[] = "Scarshield Legion"; 
    $se[] = "Shadowsong"; 
    $se[] = "Shattered Halls"; 
    $se[] = "Shattered Hand"; 
    $se[] = "Silvermoon"; 
    $se[] = "Skullcrusher";
    $se[] = "Spinebreaker"; 
    $se[] = "Sporeggar";
    $se[] = "Steamwheedle Cartel";
    $se[] = "Stormrage"; 
    $se[] = "Stormreaver";
    $se[] = "Stormscale";
    $se[] = "Sunstrider"; 
    $se[] = "Sylvanas";
    $se[] = "Talnivarr";
    $se[] = "Tarren Mill";
    $se[] = "Terenas"; 
    $se[] = "Terokkar";
    $se[] = "The Maelstrom"; 
    $se[] = "The Sha'tar"; 
    $se[] = "The Venture Co."; 
    $se[] = "Thunderhorn";
    $se[] = "Trollbane"; 
    $se[] = "Turalyon"; 
    $se[] = "Twilight's Hammer";
    $se[] = "Twisting Nether"; 
    $se[] = "Vashj"; 
    $se[] = "Vek'nilash"; 
    $se[] = "Wildhammer"; 
    $se[] = "Xavius";
    $se[] = "Zenedar"; 

// French Realms    
    $se[] = "Arak-arahm"; 
    $se[] = "Arathi"; 
    $se[] = "Archimonde";
    $se[] = "Chants éternels"; 
    $se[] = "Cho'gall"; 
    $se[] = "Confrérie du Thorium"; 
    $se[] = "Conseil des Ombres";
    $se[] = "Culte de la Rive noire"; 
    $se[] = "Dalaran";
    $se[] = "Drek'Thar";
    $se[] = "Eitrigg"; 
    $se[] = "Eldre'Thalas"; 
    $se[] = "Elune"; 
    $se[] = "Garona"; 
    $se[] = "Hyjal"; 
    $se[] = "Illidan";
    $se[] = "Kael'thas"; 
    $se[] = "Khaz Modan"; 
    $se[] = "Kirin Tor";
    $se[] = "Krasus"; 
    $se[] = "La Croisade écarlate"; 
    $se[] = "Les Clairvoyants"; 
    $se[] = "Les Sentinelles"; 
    $se[] = "Marécage de Zangar"; 
    $se[] = "Medivh"; 
    $se[] = "Naxxramas";
    $se[] = "Ner'zhul"; 
    $se[] = "Rashgarroth"; 
    $se[] = "Sargeras"; 
    $se[] = "Sinstralis";
    $se[] = "Suramar"; 
    $se[] = "Temple noir"; 
    $se[] = "Throk'Feroth"; 
    $se[] = "Uldaman"; 
    $se[] = "Varimathras"; 
    $se[] = "Vol'jin"; 
    $se[] = "Ysondre";

// Spanish Realms    
    $se[] = "C'Thun";
    $se[] = "Dun Modr"; 
    $se[] = "Exodar"; 
    $se[] = "Los Errantes"; 
    $se[] = "Minahonda";
    $se[] = "Sanguino";
    $se[] = "Shen'dralar";
    $se[] = "Tyrande";
    $se[] = "Uldum"; 
    $se[] = "Zul'Jin";

// Russion Realms - No entries due to conversion problems with the special chars

?>