$(function() {
var board_en_names = [
'2nd_NTUCCC',
'8words',
'97LSS-novel',
'a-diane',
'A-do',
'A-SI-53-2T',
'A-SI-54-3T',
'A-SI-55-1T',
'A-SI-58',
'A-SI-95-1T',
'A-SI_2-1T',
'A-UPUP',
'A1-GP',
'Abin',
'Aboriginal',
'AboutBoards',
'AboutNew',
'About_Clubs',
'About_Life',
'Absoundtrack',
'ac2006',
'Academics',
'Acad_discuss',
'Accessory_3C',
'Accounting',
'Ace-Combat',
'ACG-EX',
'ACG_island',
'ACMCLUB',
'Actuary',
'AC_Music',
'AC_Sale',
'AD',
'Adachi',
'adulation',
'AdvEduUK',
'Aerobics',
'AGEC-camp',
'AGO',
'Agri_Service',
'AGRsport',
'ai-photo',
'AIESEC',
'AIKA',
'Aikido',
'AiMorinaga',
'AION',
'AirForce',
'AiYazawa',
'Ajax',
'Akimine',
'AkinoMatsuri',
'ALI_Project',
'ALL-RUSSIANS',
'allergy',
'ALLPOST',
'AllTogether',
'ALS-93-2',
'ALSA',
'Alteil',
'Alternative',
'Ame_Casual',
'AMI',
'Anchors',
'Ancient',
'Android',
'AndroidDev',
'AngelCity',
'AngelicaLee',
'Angelique',
'Ang_Lee',
'AnimalForest',
'AnimalGoods',
'ANIMAX',
'AnimMovie',
'AnneRice',
'Anti-Cancer',
'Anti-Fake',
'anti-hurt',
'Anti-ramp',
'AntiVirus',
'ANZAI',
'AOE',
'AppleCareer',
'AppleDaily',
'AprilSky',
'aqua-shop',
'AquaPet',
'Aquarium',
'Aquarius',
'ArakawaCow',
'Arch-model',
'Architecture',
'ArcSystemFTG',
'Aries',
'Arina',
'arm54-1_81AR',
'arm55-1_4B2C',
'arm55-1_4B3C',
'arm55-2_1B2C',
'arm55-2_2B1C',
'arm56-1_2BWP',
'arm56-2_1B1C',
'arm56-2_2B3C',
'arm56-2_3B2C',
'arm56-2_5B3C',
'arm58-2_5B3C',
'arm95-3_1B2C',
'Army-Sir',
'army542DrWPN',
'army_51-2T',
'army_52-2T',
'army_53-2T',
'army_54-1T',
'army_54-2T',
'army_55-2T',
'army_56-1T',
'army_56-2T',
'army_57-2T',
'army_58-1T',
'army_58-2T',
'army_59-1T',
'army_59-2T',
'army_60-1T',
'Aromatherapy',
'Array',
'Art-Service',
'ArtCenter',
'Artfilm',
'Arthropoda',
'Arti-Sir',
'Arti53-1t',
'Arti53-2t',
'Arti54-1t',
'Arti54-2t',
'Arti55_2T',
'Arti59_1T',
'Artistic-Pho',
'Arti_60-1T',
'ASAP_1st',
'asciiart',
'ascii_wanted',
'ASES-Taiwan',
'AsiaMovies',
'Asiantennis',
'ask',
'AskBoard',
'ASM',
'Associations',
'AstralMaster',
'ATCC',
'Atheism',
'Audiophile',
'AudioPlayer',
'AUS_Tennis',
'AVA-Online',
'AVEncode',
'Avenger2B3C',
'Aves',
'Aviation',
'AviationGame',
'Ayana_Kana',
'A_A',
'b00902HW',
'b00902xxx',
'b01902HW',
'b01902xxx',
'b02902HW',
'b855060xx',
'b865060xx',
'b885060xx',
'b89902xxx',
'b90902xxx',
'b91902xxx',
'b92902xxx',
'b93902HW',
'b93902xxx',
'b94902HW',
'b94902xxx',
'b95902HW',
'b95902xxx',
'b96902HW',
'b96902xxx',
'b97902HW',
'b97902xxx',
'b98902HW',
'b98902xxx',
'b99902HW',
'b99902xxx',
'BabyFace',
'BabyMother',
'BabyProducts',
'BadmintnClub',
'Badminton',
'bag',
'baking',
'Bank_Service',
'Barista',
'barterbooks',
'Baseball',
'Baseball_BM',
'Baseball_Sim',
'Battery',
'BBSmovie',
'BB_Online',
'BeanFlower',
'Beauty',
'BeautyBody',
'BeautySalon',
'bedroom',
'Being',
'Bellydance',
'BensonYeh',
'BeverageRoom',
'bi-sexual',
'bicycle',
'bicycle-tour',
'Big2',
'BigPhysCup',
'biker',
'BikerShop',
'Bilk',
'Billiard',
'Bingo',
'BioHazard',
'Bioindustry',
'BIOSTAT',
'Birthday',
'BK-Tower',
'BL',
'BlackBerry',
'Blind_Mobile',
'blind_pc',
'Blog',
'BloodType',
'BlueAngel',
'Bluetooth',
'Board',
'BoardGame',
'book',
'bookhouse',
'BookService',
'Bowling',
'Boxing',
'boxoffice',
'Boy-Girl',
'Bread',
'Brethren',
'bridge',
'BridgeClub',
'BROADWAY',
'Broad_Band',
'Broker',
'Browsers',
'BT95A3-4-5-6',
'BT96A9-10',
'BT97GS14',
'BT97TP12-13',
'Buddha',
'Buddhism',
'Buffalo',
'Bugtraq',
'Bunco',
'Bus',
'BusTimes',
'BuyTogether',
'Buzz_Act',
'Buzz_Enter',
'Buzz_Memory',
'Buzz_NewBd',
'Buzz_Service',
'Buzz_Suggest',
'Buzz_Theater',
'BWY',
'Cabal',
'cabala',
'Cad_Cae',
'CAFENCAKE',
'calligraphic',
'Calligraphy',
'Campus-plan',
'CampusHOPE',
'CampusTour',
'Cancer',
'Canned',
'Capoeira',
'Capricornus',
'car',
'car-pool',
'Car-rent',
'CAR-TUNING',
'CaraQ_City',
'CareerLady',
'CareerPlan',
'Carnegie0121',
'CarShop',
'cat',
'CATCH',
'Catholic',
'cathy',
'CCFamily',
'ccucc',
'CCUFCS-8th',
'CCWSA',
'CCYS',
'CD-R',
'CDMA',
'CECUP',
'CFAiafeFSA',
'CFantasy',
'CFP',
'Cga',
'CGCU',
'CGI-Game',
'CGUMHVT',
'CH303',
'chageworld',
'ChainChron',
'ChangAiLin',
'Chang_Course',
'Chan_Mou',
'Chat82gether',
'chatskill',
'Cheerleader',
'CheInf_53-2T',
'Chemcup',
'chess',
'Chi-Gong',
'Chia-Yi',
'Chibi',
'chicken',
'chienchen',
'Childhood',
'child_books',
'Child_Psy',
'China',
'China_travel',
'Chinese',
'ChineseChess',
'ChineseOpera',
'ChineseTeach',
'CHING',
'ching-yi',
'ChinYun',
'chocolate',
'Christianity',
'Chuang',
'ci-poetry',
'CIAEAE',
'Civil',
'ck14th-flute',
'CKBA',
'CKEFGISC-1st',
'CKEFGISC-2nd',
'CKEFGISC-3rd',
'CKEFGISC-4th',
'CKEFGISC-5th',
'CKEFGISC-6th',
'CKEFGISC-7th',
'CKEFGISC-8th',
'CKEFGISC-9th',
'CKEFGISC10th',
'CKEFGISC11th',
'CKEFGISC12th',
'CKTFGroller',
'Clamp',
'ClarkU',
'CLOCA',
'Clothes',
'Cloud',
'ClubChief',
'ClubNewBoard',
'CLUB_KABA',
'CMstudents',
'CMU-kendofrd',
'CMU_Guitar42',
'coconut',
'CodeJob',
'Coffee',
'coincidence',
'CollegeForum',
'ComeHere',
'ComGame-New',
'ComGame-Plan',
'Comic',
'ComicHouse',
'Comic_Techn',
'Coming_EE',
'Commonwealth',
'CompBook',
'Competence',
'com_syllabus',
'ConcertoGate',
'Config',
'Confucianism',
'consumer',
'Contacts',
'cookclub',
'cookcomic',
'cosplay',
'Couchsurfing',
'couple',
'CourtBasebal',
'CourtBasketB',
'CourtFootBal',
'CourtGeneral',
'CourtSports',
'CPAC_Sinica',
'Cram_Service',
'Cram_Talk',
'CrazyArcade',
'creditcard',
'Cricket',
'Crime_Movie',
'Criminal_law',
'criminology',
'CrossGate',
'CrossStrait',
'Cross_Life',
'Cross_talk',
'Crystal',
'CSCamp2001',
'CSCamp2002',
'CSCamp2004',
'CSCamp2005',
'CSCamp2006',
'CSCamp2007',
'CSCamp2009',
'CSCamp2010',
'CSCamp2011',
'CSCamp2012',
'CSCamp2K',
'CScamp98',
'CScamp99',
'CSCommunity',
'CSCouncil',
'CSIEACA',
'CSIEACT',
'CSIECourse',
'CSIECUP',
'csiegeneral',
'CSIEgraduate',
'CSIEWEB',
'CSIE_Archi',
'CSIE_ASM',
'CSIE_BASKET',
'CSIE_DBMS',
'CSIE_GBasket',
'CSIE_ICE',
'CSIE_Mahjong',
'CSIE_Network',
'CSIE_OS',
'CSIE_Pool',
'CSIE_R202',
'CSIE_R204',
'CSIE_R219',
'CSIE_R221',
'CSIE_Service',
'CSIE_SOCCER',
'CSIE_SWIM',
'CSIE_Talk',
'CSIE_TENNIS',
'CSIE_TTENNIS',
'CSIE_Volley',
'CSIE_WSLAB',
'CSMU-CM-OP',
'CSonline',
'CSSE',
'CS_Badminton',
'cs_dm2007',
'CS_IGO',
'CS_SLT2005',
'CS_Softball',
'CS_TEACHER',
'cthdscout',
'CTSP',
'CurAffairs',
'customers',
'cvhn',
'CVS',
'CY-Aged02',
'CYCU_BME33XD',
'CYCU_ICE90AB',
'cyc_c',
'CYPA',
'CZE-SVK',
'C_and_CPP',
'C_CenterWork',
'C_Chat',
'C_ChatBM',
'C_Enter',
'C_JapanBoard',
'C_MemoryWork',
'C_Question',
'C_Sharp',
'C_VoiceBoard',
'C_WorkBoard',
'DABA',
'DailyArticle',
'dance',
'danceforever',
'DarkChess',
'DarkSwords',
'Dart',
'Database',
'DATU_CUP',
'DC',
'DC_SALE',
'DDS_Imagine',
'DeadOrAlive',
'Deco_Online',
'DEKARON',
'Delicious',
'Delivery',
'Depstore',
'Derrick',
'Design',
'Detective',
'Deutsch',
'Deutsch_2K',
'DFBSD_bugs',
'DFBSD_commit',
'DFBSD_docs',
'DFBSD_kernel',
'DFBSD_submit',
'DFBSD_test',
'DGS',
'DIABLO',
'DiabloEX',
'Diary',
'dictionary',
'DieCast',
'DietDiary',
'Dietician',
'Digitalhome',
'digitalk',
'Digital_Art',
'DirectSales',
'Disabled',
'DiscreteMath',
'DiscuService',
'Disney',
'Dist-Com',
'DistantLove',
'Divina',
'divination',
'DivingSport',
'Divorce',
'DJonline',
'DNF',
'Doctor-Info',
'documentary',
'dog',
'DollHouse',
'Dolls',
'DotA2',
'DoubleMajor',
'Doujinshi',
'DPP',
'Dragonica',
'DragonNest',
'DragonQuest',
'dragonraja',
'Drama',
'Drama-Ticket',
'Drama1968',
'drawing',
'Dreamland',
'dreams-wish',
'Drink',
'Drum_Corps',
'DSLR',
'DukeTIP99',
'DummyHistory',
'DV',
'Dynasty',
'E-appliance',
'e-Business',
'e-coupon',
'e-seller',
'e-shopping',
'EAETA',
'EARL_CAIN',
'Earth_envi',
'EAseries',
'eat-disorder',
'EatToDie',
'EBCTBE',
'Eclipse',
'eco',
'Econ-Cup',
'Economics',
'Editor',
'Education',
'EESummerCamp',
'EE_Comment',
'EE_DSnP',
'EE_Service',
'egg-exchange',
'elderly',
'Elder_CK-CCC',
'Elephants',
'ELSWORD',
'Employee',
'emprisenovel',
'EMS',
'Emulator',
'Eng-Class',
'EngTalk',
'ENG_Service',
'Eou-cup',
'EpochTimes',
'equal_change',
'esahc',
'ESC',
'eslite',
'Espannol',
'eSports',
'ES_CUP',
'Ethics',
'ETS_residual',
'EuropeanCar',
'EuropeTravel',
'EVA',
'EverQuest2',
'eWriter',
'ewsoft',
'Examination',
'Exchange',
'exchange_3C',
'Exotic_Pet',
'Expansion07',
'EzHotKey',
'EZsoft',
'F-Market',
'Facebook',
'FacebookBM',
'facelift',
'Falcom',
'FaLunDaFa',
'FamilyCircle',
'Fann_Wong',
'Fantasy',
'FantaTennis',
'fashion',
'FashionDIY',
'fastfood',
'fatworld',
'FB_announce',
'FB_bugs',
'FB_chat',
'FB_current',
'FB_cvs',
'FB_doc',
'FB_hackers',
'FB_ports',
'FB_questions',
'FB_security',
'FB_smp',
'FB_stable',
'FB_svn',
'FCU_SB_CLUB',
'Federer',
'Fei-cat',
'female-shoes',
'female_club',
'feminine_sex',
'Feminism',
'Fencing',
'Fencing_club',
'FengShui',
'FestivalPark',
'FEZ',
'Fiction',
'fightforland',
'FigureSkate',
'Film-Club',
'Final-Cut',
'FinalFantasy',
'Finance',
'FinanceCup',
'FinanceNCO97',
'finding',
'FineArt',
'FiremanLife',
'first-wife',
'firsttime',
'FishShrimp',
'FITNESS',
'Fiveclub',
'fivesix',
'five_chess',
'Fix-Network',
'FixMyHouse',
'Flash',
'Flickr',
'flyFF',
'flying',
'Flying_UP',
'FMPAT',
'FMS-Taiwan',
'Folklore',
'FON',
'Food',
'Foolshome',
'FootballGirl',
'foreigner',
'ForeignEX',
'ForeignGame',
'Foreign_Inv',
'FOREST_BIO',
'FORMULA1',
'forsale',
'Fortran',
'Fortune',
'Francais',
'FRA_hotties',
'FreeBSD',
'FreeBuffet',
'Freeline',
'FreeStyle',
'Free_box',
'free_league',
'Frgn_spouse',
'friends',
'fruits',
'FSS',
'FTP',
'FujisakiRyu',
'FujitsuCUP',
'FumeiYasushi',
'FuMouDiscuss',
'Fund',
'funeral',
'Funtown',
'GaaaN',
'Galaxy',
'gallantry',
'GambleGhost',
'Game-Talk',
'GameDesign',
'GAMEMUSIC',
'Gamesale',
'GANDI',
'gardener',
'Garena',
'Garfield',
'Gary_chaw',
'GatoShoji',
'gay',
'GAYCHAT',
'GBasketballT',
'GBR_Tennis',
'Gemini',
'gender-child',
'gender-TIE',
'gender-women',
'GEPT',
'German',
'GermanGarden',
'GermanTennis',
'Gersang',
'GetBackers-s',
'GetMarry',
'GE_online',
'GFonGuard',
'GGFR',
'GHIBLI',
'GIEE_SoC_V',
'gift',
'Gindis',
'Gipcy',
'GirlComics',
'GirlE_MiliW',
'GirlIdolUnit',
'give',
'GL',
'GLB',
'global_univ',
'GMAT',
'GO',
'GO-KART',
'Goe-Bio',
'GOFORFIENDS',
'Gold27',
'Golden-Award',
'Golf',
'GONZO',
'GoodNews',
'GoodPregnan',
'GoodShop',
'GOODSTARS',
'GooGin',
'Google',
'Gossiping',
'Gov_owned',
'Gra-Travels',
'Grad-ProbAsk',
'Gradol',
'graduate',
'GraduateCram',
'GrassLand',
'GRE',
'GreenParty',
'greetnew',
'GTA',
'GuideDog',
'GuildWars',
'GuiMiscamp',
'GuineaPig',
'GuJian',
'Gulong',
'GVOnline',
'gymnastics',
'haiku',
'hairdo',
'hair_loss',
'hakka',
'Hakka-Dream',
'Hamster',
'Han-Lin',
'HandBall',
'Handiwork',
'HandMade',
'HANGUKMAL',
'happy',
'happy-clan',
'hardware',
'HardwareSale',
'HarmConcert',
'HarryPotter',
'HatandCap',
'Hate',
'HatePicket',
'HatePolitics',
'HateP_Picket',
'Hattrick',
'havardtour89',
'Hayashibara',
'Headphone',
'Health_Life',
'heart',
'Hearthstone',
'HELLOWORLD',
'HelpBuy',
'HEonline',
'HerbalPlant',
'HerbTea',
'Hermit_Crabs',
'Hero',
'Heva',
'Hightech',
'Hiking',
'Hip-Hop_NTU',
'historia',
'HISTORY',
'Hiyo',
'HK-movie',
'HKMCantonese',
'HK_Comics',
'HLCO',
'hockey',
'Holic',
'HoloTW',
'HolySee',
'home-sale',
'homemaker',
'HomeTeach',
'HON',
'HorieYui',
'Horror',
'HotBlood',
'Hotel',
'HOTS',
'hotspring',
'HOT_Game',
'howtztravel',
'HPAIR',
'HRM',
'HsiaYu',
'HT_service',
'Huashin',
'HuaXiaYouth',
'humanity',
'HumService',
'Hunter',
'hurry-up',
'Hu_Yen_2000',
'Hu_Yen_2004',
'Hu_Yen_99',
'HwangYih',
'hypermall',
'hypnotism',
'IA',
'IC-Card',
'ICDESIGN',
'IChO-CAMP',
'ICtribe',
'IdolMaster',
'ID_Finance',
'ID_GetBack',
'ID_Multi',
'ID_Problem',
'iELC',
'IELTS',
'IGuanTao',
'Ikariam',
'ILDiscussion',
'ILoveCEP',
'ILSRS',
'image',
'IME',
'IMICS',
'IMO_Taiwan',
'In-Nco-97-2T',
'Ind-travel',
'India-movie',
'Indie-Film',
'Inference',
'INFOCUP2006',
'Infor_54_1',
'Info_54-1T',
'Info_96-1',
'Ingress',
'InlineSkate',
'inshe',
'Instant_Food',
'Instant_Mess',
'Insurance',
'interdreams',
'Interior',
'interpreter',
'IntlShopping',
'intltrade',
'Intro_Comp',
'iPhone',
'iPod',
'IPv6',
'IRIS_ONLINE',
'Ironman',
'IrresCaptain',
'isLandTravel',
'IT-cup',
'Italiano',
'Itchie',
'ITI',
'ITI_100B',
'IVE',
'JAM_Project',
'japanavgirls',
'Japandrama',
'Japanhistory',
'JapanIdol',
'JapanMovie',
'JapanStudy',
'Japan_Travel',
'java',
'jawawa',
'JD_Lover',
'Jeans',
'JeffLau',
'jersey',
'Jess_Lee',
'JesusLove',
'Jewelry',
'Jin-Dan',
'Jing-Ru',
'jingle',
'Jinglun',
'JinYong',
'JJ',
'JLPT',
'JLYL_Service',
'job',
'JOB-Hunting',
'Joi',
'joke',
'joyinDIY',
'JPliterature',
'JRockClub',
'JSonline',
'Judo',
'junji-ITO',
'junkfood15',
'JX3',
'K-Kawaguchi',
'kachaball',
'Kaiouki',
'Kakinouchi',
'KanColle',
'KangYung',
'Kaohsiung',
'KaoriEkuni',
'kapilands',
'KARATE',
'kartrider',
'katsura',
'kawaii',
'Kawashita',
'Keelungfan',
'KenAkamatsu',
'kendo',
'Kenji',
'KeyBoards',
'keys-81',
'Key_Mou_Pad',
'KFWorld',
'KGA_CUP',
'KHPT_Service',
'KIDs',
'killercorp',
'kindness',
'KingdomHuang',
'Kinoshita',
'KishimotoBro',
'KITCHAN',
'KitchenUsage',
'KITE',
'Kitty_Sanrio',
'KMT',
'KMU_CIA',
'KnightBay',
'KNTUACSA',
'kodomo',
'Koei',
'KOF',
'KoreaDrama',
'KoreaStudy',
'Korea_Star',
'Korea_Travel',
'KOTDFansClub',
'KR_Entertain',
'KShistoryACG',
'KTV',
'kualab',
'kugimiya',
'Kyoto_Ani',
'Ladies_Digi',
'Lambda',
'Lan-Games',
'lan-shen',
'LangService',
'Language',
'Laser_eye',
'LaTale',
'LaTeX',
'Latina',
'Latin_AM',
'LAW',
'LawsuitSug',
'Lawyer',
'Lawyer_93-3',
'Lawyer_93-4',
'law_Newboard',
'Law_Service',
'LCD',
'LDS_Dance',
'LeafKey',
'learnyf',
'Learn_Buddha',
'LeeSangmi',
'LEFTBANK',
'Lefty',
'Left_Village',
'LegalTheory',
'Leo',
'lesbian',
'Letters',
'letter_Intro',
'LGBT_SEX',
'Libra',
'library',
'Lib_Service',
'License',
'LicenseShop',
'Life',
'lifeguard',
'lifeguard06',
'lifeguard08',
'Lifeismoney',
'LifeNewboard',
'LifeRecallBM',
'LifS_Service',
'LightBlue',
'LightNovel',
'Lineage',
'LineageII',
'Linguistics',
'Linux',
'LinuxDev',
'Lions',
'literature',
'Literprize',
'LitService',
'Little-Games',
'LittleFight',
'littlegift',
'Liu',
'Live',
'LivingGoods',
'LoL',
'LoL_Picket',
'Lomo',
'Lonely',
'LordsOfWater',
'lostsleep',
'love',
'Love-GoPets',
'love-vegetal',
'Loveboat',
'LoveGame',
'LoveLive',
'LSCup',
'LTTCJapanese',
'Lucky',
'LuLaLa29th',
'LUNA',
'LunarGazer',
'LuniaWar',
'L_Astrology',
'L_BeautyCare',
'L_Block',
'L_BoyMeetsGi',
'L_FoodAndDri',
'L_GlobalView',
'L_HappyLivin',
'L_LifeInfo',
'L_LifeJob',
'L_LifePlan',
'L_PTTAvenue',
'L_PTTHealth',
'L_Recreation',
'L_RelaxEnjoy',
'L_SecretGard',
'L_ShoppingMa',
'L_TaiwanPlaz',
'L_TalkandCha',
'L_TradeCente',
'L_Traveling',
'M-Business',
'MA',
'maaya',
'Mabinogi',
'MabinogiHero',
'MAC',
'MacDev',
'MacGame',
'Macross',
'MacShop',
'Magazine',
'Magic',
'Magic_Center',
'Magic_info',
'mahavaipulya',
'Maiden_Road',
'MakeUp',
'Management',
'Mancare',
'Map-Guide',
'Maple',
'MapleStory',
'Mario',
'Marketing',
'marriage',
'MartialArts',
'marvel',
'Marxism',
'MasamiTsuda',
'Master_D',
'Mathematica',
'MATLAB',
'MavisHsu',
'MayClass',
'MayDay',
'Mayn',
'MBA',
'MBA2003camp7',
'MCAthletics',
'MD-WALKMAN',
'MdnCNhistory',
'ME-FR_2002',
'Mealler',
'Mechanical',
'medache',
'MedChorusROC',
'media-chaos',
'Media-work',
'meditation',
'MedRock',
'MedSharks',
'medstudent',
'MED_serve',
'MED_Service',
'Meeia',
'MegumiOgata',
'Mei-Feng',
'Melody',
'MelodyLyric',
'memento',
'MemoriesOff',
'Memory',
'MenTalk',
'MetalGear',
'MGT_Service',
'MH',
'Miao_Meiyee',
'Michigan_96',
'midwaywisdom',
'Militarylife',
'Mind',
'Minecraft',
'Mineko',
'Minekura',
'MingTung',
'MINORI',
'minoru',
'MIS',
'MishimaYukio',
'mitsurou',
'MIU',
'Mix_Match',
'Mizuki_Nana',
'MJ',
'MJ_JP',
'mknoheya',
'MKSH-95-6',
'MKSH50th306',
'MLB',
'MLBGAME',
'MMA',
'MMD_TDCG',
'mobile-game',
'MobileComm',
'MobilePicket',
'mobilesales',
'Modchip',
'model',
'Model-Agency',
'Model-talks',
'Mollie',
'money',
'Monkeys',
'Monotheism',
'MONSTER',
'MoSiang',
'Motel',
'MotorClub',
'motor_detail',
'Moto_GP',
'MountainClub',
'MoveHouse',
'movie',
'Movie-Score',
'MP',
'MP3-player',
'MP53-1',
'MP95-1T',
'MP95-2T',
'MP95-3T',
'MP96-1',
'MP96-2T',
'MP98-1T',
'MP_53-2T',
'MP_55',
'MP_57',
'Mr-Red',
'MRT',
'MSECUP',
'MU',
'MuBin',
'mud',
'mud_doom',
'mud_jy',
'mud_mars',
'mud_sanc',
'Multi-lingua',
'multi-lovers',
'Murakami_Ryu',
'MuscleBeach',
'museum',
'MusicComic',
'MusicGame',
'MusouOnline',
'MVPlive',
'MY-Camp',
'MyCIA',
'MyLittlePony',
'MysteryStory',
'N-D-Mystery',
'NailSalon',
'Name',
'NARUTO',
'Natal',
'Native',
'nature-easy',
'Navy',
'nb-shopping',
'NBA',
'NBAGAME',
'NBALive',
'NBA_Film',
'NCCU',
'NCCU-ILI',
'NCCU00_CS',
'NCCU01_JOUR',
'NCCU01_MAD',
'NCCU02_AD',
'NCCU02_MJOUR',
'NCCU03_RTV',
'NCCU04_MJOUR',
'NCCU04_RTV',
'NCCU05_MJOUR',
'NCCU06_ECE',
'NCCU06_MAD',
'NCCU06_MJOUR',
'NCCU07_MAD',
'NCCU07_MJOUR',
'NCCU08_MAD',
'NCCU08_MJOUR',
'NCCU09_MAD',
'NCCU09_MJOUR',
'NCCU10_MJOUR',
'NCCU98_AD',
'NCCU98_JOUR',
'NCCU99_MIS',
'NCCUcommerce',
'NCCULawClub',
'NCCURPH',
'NCCU_BEAUTY',
'NCCU_CCUDP',
'NCCU_CHI_TCH',
'NCCU_EDU',
'NCCU_EDUGRAD',
'NCCU_ETP',
'NCCU_Exam',
'NCCU_ICE',
'NCCU_Info',
'NCCU_MandA',
'NCCU_NICEBOY',
'NCCU_SHUAI',
'NCCU_TCSL_97',
'NCCU_thanks',
'NCDC',
'NChorus',
'NCKU-chat',
'NCSI_98A6',
'NCSI_98AG3',
'NCSU-SUMMER',
'NCTU_basebal',
'NDS',
'NED-BEL-LUX',
'need_student',
'NeoSteam',
'Nethood',
'NetRumor',
'NetSecurity',
'Network',
'Network_Sim',
'NeverWinter',
'NewActivity',
'NewAge',
'NewBoard',
'NEW_ROC',
'NFL',
'NFLLD',
'NFS',
'NHistory93',
'NHistory94',
'NHistory95',
'NHLTSC',
'Nicholas_Teo',
'Niconico',
'NightLaw',
'Nightmarket',
'NihonBook',
'NIHONGO',
'NILSA',
'nine-night',
'nineinnings',
'NinjaTurtles',
'Ninomiya',
'Nintendo',
'NitroPlus',
'no2good',
'nobunyaga',
'NobuOnline',
'Nolan',
'Non-Graduate',
'North-Scup',
'North-SV-Cup',
'northtrans',
'Nostale',
'Note',
'Notebook',
'NotoMamiko',
'novel',
'NPB_Online',
'NPWB3R201',
'NSRS',
'NS_Online',
'NTS_55_1T',
'NTU',
'NTU-Aikido',
'NTU-ALL',
'NTU-Archery',
'NTU-AT',
'NTU-Baseball',
'NTU-Breaking',
'NTU-CFE',
'NTU-CHKongFu',
'NTU-coffee',
'NTU-CONSERVE',
'NTU-CTW',
'NTU-DDYP',
'NTU-DMCC',
'NTU-dolphin',
'NTU-dreamer',
'NTU-EMT1',
'NTU-Exam',
'NTU-Fantasy',
'NTU-FD',
'NTU-Flamenco',
'NTU-Graduate',
'NTU-Guitar',
'NTU-HOLATEAM',
'NTU-IAS',
'NTU-IYIU',
'NTU-Jazz',
'NTU-Jigsaws',
'NTU-JOHNNYS',
'NTU-JPdance',
'NTU-Juggling',
'NTU-Karate',
'NTU-LifePhi',
'NTU-MAGIC',
'NTU-Makeup',
'NTU-MD',
'NTU-meddance',
'NTU-MJ',
'NTU-OSHO',
'NTU-PDC',
'NTU-Poker',
'NTU-Puzzle',
'NTU-Qin',
'NTU-Rail',
'NTU-Riot',
'NTU-Taipei',
'NTU-TAP',
'NTU-Textbook',
'NTU-TLC',
'NTU-ukulele',
'NTU-WingChun',
'NTU-Zither',
'NTU3C',
'NTUacadem',
'NTUACenter',
'NTUActivity',
'NTUACT_01',
'NTUACT_02',
'NTUACT_03',
'NTUACT_04',
'NTUAct_Club',
'NTUAikido',
'NTUAL-ARCHER',
'NTUAMLC',
'NTUAR',
'NTUARTpro',
'NTUAST',
'NTUastclub',
'NTUAviation',
'NTUAWEC',
'NtuBaChi',
'NTUbadminton',
'NTUBASKETBAL',
'NTUBC',
'NTUBeetles',
'NTUBilliard',
'NTUboardgame',
'NTUbus',
'NTUBW',
'NTUCAREER',
'NTUCB',
'NTUCCC',
'NTUCCG',
'NTUcchelp',
'NTUCGM',
'NTUCGS',
'NTUChallenge',
'NTUCHESS',
'NTUChmusic',
'NTUChorus',
'NTUCIE',
'NTUCivilism',
'NTUCLS',
'NTUClubs',
'NtuCMArts',
'NTUCMCC',
'NtuCompoClub',
'NTUcontinent',
'NTUCOOP',
'NTUCOS',
'NTUcourse',
'ntucrc',
'NTUCYCLUB',
'ntucyls',
'NTUDalawasao',
'NTUDancing',
'NTUdebate',
'NTUDesign',
'NTUDMCC',
'NTUDRC',
'NTUDT',
'NTUEBA',
'NTUeducation',
'NTUEE098',
'NTUEE099',
'NTUEE100',
'NTUEE101',
'NTUEE102',
'NTUEE103',
'NTUEE104',
'NTUEE105',
'NTUEE106',
'NTUEE106HW',
'NTUEE107',
'NTUEE107HW',
'NTUEE108',
'NTUEE108HW',
'NTUEE110',
'NTUEE110HW',
'NTUEE111',
'NTUEE111HW',
'NTUEE112',
'NTUEE112HW',
'NTUEE113',
'NTUEE113HW',
'NTUEE114HW',
'NTUEE115',
'NTUEE115HW',
'NTUEE116',
'NTUEE117',
'NtuEightStep',
'NTUEngDeb',
'NTUEngSA',
'NTUEP',
'NTUFD',
'ntufiction',
'Ntuflower',
'NTUfolkdance',
'NTUFootball',
'NTUFS',
'NTUFyLife',
'NTUG-ANATOMY',
'NTUG-PHYSIOL',
'NTUG-TOXIC',
'NTUGF_Old',
'NTUGO',
'NTUGOCS',
'NTUGOLF',
'NTUGolfClub',
'NTuGraduate',
'NTUGSA',
'NTUGT86',
'NTUHANDBALL',
'NTUHASSE',
'NTUHI',
'NTUHKMSA',
'NTUHorse',
'NTUhospital',
'NTUice',
'NTUICPSC',
'NTUIMA',
'NTUISIS',
'NTUISO',
'NTUJapan',
'NTUjewel',
'NTUJudo',
'NTUJueMin',
'NTUKB',
'NTUKENDO',
'ntukf',
'NTUKS',
'NTUKS_Elders',
'NTUKunOpera',
'NTULabor',
'NTULCSA',
'NTULibrary',
'NTULulala',
'NTUMac',
'NTUManVolley',
'NTUmassage',
'NTUMC',
'NTUMCTT',
'NTUMG',
'NTUMiaoli',
'NTUMotorClub',
'NTUmoviefest',
'NTUMPS',
'NTUMRC',
'NTUMUN',
'NTUmusical',
'NTUmusicgame',
'NTUMystery',
'NTUNANO',
'NTUnba2005',
'NTUnewheart',
'NTUNewPlace',
'NTUNewsForm',
'NTUNIC',
'NTUniNews',
'NTUNL',
'NTUNOTE',
'NTUnSA',
'NTUNTE',
'NTUOCSA',
'NTUPAteam',
'NTUPBC',
'NTUPHOTO',
'NTUPiano',
'NTUPMC',
'NTUPOD',
'NTUPoem',
'NTUPR',
'NTUQX',
'NTUrefined',
'NTURockClub',
'NTURugbyTeam',
'NTUSA',
'NTUSC',
'NTUScout',
'NTUSCPD',
'NTUSCSA',
'NTUscuba',
'NTUSealCarve',
'NTUSFA',
'NTUSG',
'NTUShingYi',
'NTUSJ',
'NTUsk8board',
'NTUSLC',
'NTUSNews',
'NTUSNSC',
'NTUSO',
'NTUsoftball',
'NTUSSSA',
'NTUSTAR_rain',
'NTUstat',
'NTUST_NJMA',
'NtuTaiChi',
'NTUTango',
'NTUTarot',
'NTUTEAMCLUB',
'NTUTM',
'NTUTMC',
'NTUTO',
'NTUTradOpera',
'NTUTTST',
'NTUtutor',
'NTUTW-Opera',
'NTUVC',
'NTUVGC',
'NTUWCC',
'NTUWESTFOOD',
'NTUWindBand',
'NTUWRC',
'ntuwvs',
'NtuYangTai',
'NTUYoGa',
'NTU_4H_Club',
'NTU_ART',
'NTU_Beauty',
'NTU_CDVol',
'NTU_CDYoungs',
'NTU_Dai',
'NTU_DP',
'NTU_EE_ALGO',
'NTU_EE_TEST',
'NTU_GoodLife',
'NTU_Guangxi',
'NTU_NAIL',
'NTU_NICEBOY',
'NTU_OC',
'NTU_PDS',
'NTU_SKY',
'NTU_stamp',
'NTU_TA',
'NTU_trans',
'NTU_VLSI_DA',
'NTU_Zen',
'NT_Tennis_Cu',
'Numerology',
'Nurse',
'NYU89_memory',
'NYU_2002',
'o-p-children',
'ObataTakeshi',
'Ocean',
'Office',
'OgreBattle',
'Oh-GREAT',
'Oh-Jesus',
'Old-Games',
'OldGymFellow',
'Old_Egg',
'OLD_Icemen',
'OLD_STAR',
'Olivia',
'Olympics_ISG',
'ONE_PIECE',
'Oni_soul',
'ONLINE',
'onlychild',
'OOAD',
'optical',
'Option',
'Oracles',
'OrangeRoad',
'Ordnance54-2',
'ORTalk',
'othello',
'Ourmovies',
'outdoorgear',
'OverClocking',
'Oversea_Job',
'P2PSoftWare',
'PaintBall',
'painting',
'pal',
'Palmar_Drama',
'Pal_online',
'PangSir',
'Pangya',
'Paradox',
'part-time',
'part-timeBM',
'Patent',
'PathofExile',
'Paul_52-1t',
'Paul_53-1T',
'Paul_53-2T',
'Paul_54-2T',
'Paul_55-2T',
'Paul_59-1T',
'PCman',
'pc_cup',
'PC_Shopping',
'PDA',
'Peanuts',
'Penny',
'Penpal',
'PerfectWorld',
'Perfume',
'Perfume_Shop',
'Peripatos',
'Perl',
'pet',
'petwork',
'Pet_boarding',
'Pet_Get',
'pharmacist',
'Philharmonic',
'photo',
'photo-buy',
'PhotoCritic',
'PhotoEdit',
'PhotoLink',
'PHP',
'PHS',
'Phsp',
'PH_service',
'pighead',
'Pilates',
'Pingpong',
'Pisces',
'pity',
'Plant',
'PlayFootball',
'PlayStation',
'PLT',
'PM',
'Pocket',
'poem',
'poetry',
'PoetryBook',
'points',
'Poker',
'PoleStar',
'Policy',
'PoliticBM',
'PoliticLaw',
'PoliticNew',
'politics',
'Polytechnic',
'Pool-Beauty',
'Post',
'postcrossing',
'POST_CUP',
'Powerful_PRO',
'PowerTech',
'Preschooler',
'PresidentLi',
'Printer_scan',
'PRIUS',
'Prob_Solve',
'Programming',
'Progressive',
'prose',
'prozac',
'PSATW',
'PSP-PSV',
'Psychiatry',
'PttAntiBot',
'PttBug',
'PttCChess',
'Pttcoach',
'PttCurrent',
'PttFamous',
'PttFB',
'PttFive',
'PttGames',
'PttGO',
'PttLaw',
'PttLawSug',
'PttLifeLaw',
'PttNewhand',
'PttNewSport',
'PttPubRel',
'PttSuggest',
'PTT_KickOff',
'PublicBike',
'PublicIssue',
'PublicServan',
'Publish',
'PushDoll',
'PutOffShell',
'puzzle',
'PuzzleDragon',
'PVC-GK',
'Python',
'P_Management',
'QMA',
'Quant',
'QueerHabit',
'Question',
'Quincychen',
'QUT',
'Q_ary',
'rabbit',
'radio',
'RailTimes',
'railtour',
'Railway',
'RC_Sport',
'RDSS',
'RealPlaying',
'Redbud',
'Redology',
'Refresh',
'RegExp',
'regimen',
'ReikoShimizu',
'Relax',
'RelayFiction',
'Reli-curio',
'rent-exp',
'Rent_apart',
'Rent_tao',
'Rent_ya',
'Reporter',
'Reptile',
'RESIT',
'ResourceRoom',
'Re_Anonymous',
'RFonline',
'Rhinos',
'Rice194904',
'riddle',
'RIPE_gender',
'RIVER',
'RO',
'Road',
'Road_Running',
'Rockman',
'ROHAN',
'ROL_Online',
'ROM',
'RomanceGame',
'Romances',
'RootsnShoots',
'Rounders',
'roveroldbone',
'RPGMaker',
'RS',
'RTS',
'Rubiks',
'Ruby',
'Rugby',
'RumikoTWorld',
'Russian',
'rynn',
'R_Language',
'S-Asia-Langs',
'SAA2005',
'Sacred',
'Sad',
'Sagittarius',
'SailorMoon',
'Salary',
'Salesperson',
'SAN',
'San-X',
'SAN-YanYi',
'Sanfu_Gutao',
'Sangokumusou',
'sanguosha',
'Sassy-babe',
'Saxophone',
'SayLove',
'Scenarist',
'ScenicPhoto',
'scholarship',
'SciInstitute',
'Sci_Service',
'Scorpio',
'Scout',
'SC_Picket',
'SD-GundamOL',
'Seal_Online',
'SeattleBest',
'Seed_EDU',
'SEGA_MLB',
'seiyuu',
'Seiyuu_Data',
'Self-Healing',
'Service',
'ServiceInfo',
'SET',
'SetupBBS',
'Seven-up',
'SevenHand',
'Sewing',
'sex',
'SF',
'SFFamily',
'SG',
'SGS_Online',
'SG_BM',
'Shaiya',
'Shan-Wai',
'shandong',
'share',
'ShihChing',
'shinkai',
'Shinohara',
'shoes',
'Shooter-game',
'SHORTY',
'ShowOff',
'Siam-Star',
'Sign',
'SilentHill',
'Silkroad',
'Silverlight',
'Simcity',
'single',
'sinica',
'SinicaGuitar',
'sinicaPGclub',
'skating',
'skinny',
'SkiSnowboard',
'SLG',
'Slugger',
'SmallFace',
'SmashBros',
'Smite',
'SMJ',
'SMSlife',
'Snacks',
'SnipeCompany',
'Snooker',
'SO',
'So-Edition',
'SOB_CLAMP',
'SOB_Conan',
'SOB_COSPLAY',
'SOB_JoJo',
'SOB_UTENA',
'Soccer',
'SoccerClub',
'Social-Ent',
'SocS_Service',
'Softball',
'SoftballTeam',
'SoftPower',
'SOFTSTAR',
'SoftTennis',
'Soft_Job',
'soho',
'Sony-style',
'SorryPub',
'soul',
'SoundHorizon',
'SouthPark',
'SpaceArt',
'Spain_PL',
'SpecialForce',
'specialman',
'SpongeBob',
'Spore',
'Sportcenter',
'SportComic',
'SportLottery',
'SportsCard',
'SportsRecall',
'SportsShop',
'SportStack',
'Spurs',
'sp_teacher',
'squash',
'SRB-CRO',
'SRW',
'SSA',
'SSWonline',
'SS_Service',
'ST-57-1T',
'Stall',
'Stanford-99',
'Star-Clan',
'Starbucks',
'StarCraft',
'StarWars',
'stationery',
'Steam',
'Stella',
'Stock',
'StoneAge',
'Storage_Zone',
'story',
'Strathclyde',
'streetfight',
'streetsinger',
'street_style',
'STS',
'study',
'studyabroad',
'StudyGroup',
'StudyinNL',
'studyteacher',
'StupidClown',
'Sub_CS',
'Sub_DigiLife',
'Sub_DigiTech',
'Sub_DigiWare',
'Sub_GConsole',
'Sub_GOnline',
'Sub_GSports',
'Sub_GTopics',
'Sub_RolePlay',
'Sub_Strategy',
'Suckcomic',
'SuckcomicBM',
'SuckMovies',
'Sucknovels',
'Suit_Style',
'SummerCourse',
'SummonsBoard',
'SUN',
'SUNFLOWER',
'Sunrise',
'Sunrise12',
'SunriseNTU',
'Sun_Ho',
'SuperBike',
'SuperHeroes',
'Supermission',
'Supersnail',
'Supply96-2',
'Supply97-1',
'Supply97-2',
'Supply_55-1T',
'Surf',
'SurvivalGame',
'Swccyer',
'swim',
'Swimming',
'SwimWear',
'SWORD',
'SW_Job',
'T-I-R',
't-management',
'tabletennis',
'TACTICS',
'taekwondo',
'TAHR',
'Tai0rzSkate',
'TaiChi',
'Taichung34th',
'Taichung38th',
'Taichung42th',
'taichung48th',
'TaichungBun',
'Tainan',
'Taipeiman',
'TaiSu',
'TaiwanDrama',
'Taiwanlit',
'TaiwanSchool',
'TakahasiShin',
'Takarazuka',
'tale',
'TalesSeries',
'TalesWeaver',
'talk',
'TallClub',
'TAMURA',
'TAMURAYUKARI',
'TANAKA',
'Tanya',
'Taoism',
'taoyuansgi',
'TAROT',
'Tartaros',
'TAS',
'tattoo_ring',
'Taurus',
'tax',
'TAXI',
'TBC',
'TBDS',
'Tea',
'Tea91Talk',
'Teacher',
'teaching',
'TeaClub',
'Team-NTU',
'Tech_Job',
'TEDxTaida',
'teeth_salon',
'tellstory',
'TenchiMuyo',
'tenimyu',
'Tennis',
'Tennisclub',
'TennisGame',
'TennisTeam',
'tennis_life',
'Tennis_Team',
'Tera',
'Test',
'tetris',
'textbook',
'TezukaOsamu',
'TFAT',
'Theater',
'Theatre',
'TheMatrix',
'Therapist',
'TheSims',
'the_L_word',
'third-person',
'threeprogram',
'THSRshare',
'TigerBlue',
'Tin-Ha',
'TJSC',
'TKU_GF_OB',
'TL-GreatGuy',
'TLBB_Online',
'TMCSA',
'TMU2913',
'TNCF',
'toberich',
'TOEFL_iBT',
'TOEIC',
'Top_Models',
'ToS',
'ToS_Match',
'Touhou',
'Tour-Agency',
'Tour-Manager',
'Toy',
'ToyosakiAki',
'TOYPF',
'TPI47',
'TPI49',
'Trace',
'TrackField',
'Trading',
'traffic',
'Traffic_cup',
'TransBioChem',
'TransCSI',
'TransEcoAcc',
'Transfer',
'Transformers',
'transgender',
'Translate-CS',
'Translation',
'translator',
'TransLaw',
'TransPhys',
'TransPSY',
'trans_humans',
'trans_math',
'Trans_Study',
'travel',
'travelbooks',
'TravelGuy',
'travian',
'TRG',
'TribalWars',
'TRPG',
'True-Escape',
'TrueFaith',
'TS2Online',
'TSAIMingLian',
'TSU',
'Tsukasa',
'TTSHSCOUT',
'TurtleSoup',
'tutor',
'TuTsau',
'TVCard',
'TVHSshowhost',
'TW-F-Tennis',
'TW-FirstClub',
'TW-GHONOR',
'TW-history',
'TW-language',
'TW-M-Tennis',
'Tweety',
'Twelvesky',
'twin',
'TWO-MIX',
'TWopera',
'TWproducts',
'TWSU',
'TWvoice',
'TW_Minstrel',
'TYI',
'TYOS',
'tzuchi',
'UglyClub',
'UJ-RPG',
'underwear',
'unemployed',
'Uni-LawServ',
'Uni-NewBoard',
'Uni-Service',
'uni-talk',
'Unicup',
'UniDis-Serv',
'uniform',
'UnitedLaw',
'Univ_BRDance',
'Unlight',
'USCTSA',
'UST_chmusic',
'UST_Piano',
'US_Army',
'UTSAC',
'UW-Madison',
'VictoryYouth',
'Video',
'VideoCard',
'ViolateLaw',
'Violation',
'Virgo',
'VISA',
'Vision',
'visualband',
'Visual_Basic',
'vitalitydiet',
'VM',
'Vocaloid',
'VoIP',
'Volleyball',
'Volleygirl',
'Volunteer',
'W-Philosophy',
'WallBall',
'Wallpaper',
'Wan-Ray-An',
'Wanted',
'wanwan',
'WarCraft',
'WarCraftChat',
'Warfare',
'Wargame',
'WarHammer',
'Warrant',
'WarringState',
'War_Thunder',
'WATARU',
'WataseYuu',
'watch',
'WebRadio',
'Web_Design',
'WeWantSpace',
'Weyslii',
'Whatever',
'WHSoftball',
'Wii',
'WikiNTU',
'WindFantasy',
'Windows',
'WindowsPhone',
'WinMine',
'Winnie',
'WINNING_11',
'WinNT',
'wisdom',
'WomenTalk',
'wonderland',
'WongKarWai',
'WoodworkDIY',
'wordtell',
'WorkanTravel',
'WorldCup',
'WoT',
'WOW',
'WOW-TCG',
'WRC',
'Wrestle',
'wretch',
'WriteService',
'Wrong_spell',
'WX_Online',
'X-game',
'X-Legend',
'XBOX',
'XiangSheng',
'XT',
'X_Z_Zhou',
'Yabuki',
'Yakyu_spirit',
'Yale-00',
'Yale-99',
'yangboy',
'YangTaiChi',
'YangZhao',
'Yanzi',
'YASUHIRO',
'YAYA',
'YCN-Service',
'YH-FeverNite',
'YiDA',
'YK',
'YMCA',
'yo-yo',
'yoga',
'YogaGirls',
'Yoma-Masashi',
'Yon',
'yong-online',
'Yoshimoto',
'Yoshizumi',
'YoungArtist',
'youth_ddm',
'You_out',
'YOYOGA',
'YuanChuang',
'Yukari',
'YuukiHiro',
'YUYU',
'YzuDanceCrew',
'Z-Chen',
'ZACTION',
'Zastrology',
'ZCLUB',
'ZenCard',
'ZephyrOpera',
'ZFans',
'zhuxian',
'ZionChurch',
'ZOO'
];
$( "#select_board" ).autocomplete({
source: board_en_names
});
});

