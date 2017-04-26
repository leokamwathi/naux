<?php

function setReplys()
{
    logx("{SETTING REPLIES}");

/*
    $GLOBALS['isTyping'] = '
                {"recipient":{
                    "id":"'.$GLOBALS['sid'].'"
                },
                "sender_action":"typing_on"
                }';
*/
    $GLOBALS['status_info'] = '
                {"recipient":{
                    "id":"'.$GLOBALS['sid'].'"
                },
                "message":{
                    "text":"Hi '.$GLOBALS['username'].', \n
                    Your profile information.\n
                    Location:' . getField('location') . '\n
                    Job:' . getField('job') . '\n
                    Qualification:' . getField('qualification') . '\n
                    Experience:' . getField('experience') . '\n\n
                    I will send you daily notifications when I get job openings matching your requirements.\n\n
                    You can also find places around '.getField('findlocation').'.\nJust use the command find [place] eg find hotel.",
                    "quick_replies":[
                        {
                            "content_type":"text",
                            "title":"Search Jobs",
                            "payload":"search_jobs"
                        },
                        {
                            "content_type":"text",
                            "title":"Find Place",
                            "payload":"find_place"
                        },
                        {
                            "content_type":"text",
                            "title":"Edit Location",
                            "payload":"edit_location"
                        },
                        {
                            "content_type":"text",
                            "title":"Edit Job",
                            "payload":"edit_job"
                        },
                        {
                            "content_type":"text",
                            "title":"Edit Qualification",
                            "payload":"edit_qualification"
                        },
                        {
                            "content_type":"text",
                            "title":"Edit Experience",
                            "payload":"edit_experience"
                        },
                        {
                            "content_type":"text",
                            "title":"Delete Profile",
                            "payload":"delete_profile"
                        }
                    ]
                }
            }';

    $GLOBALS['status_userType']  = '
            {"recipient":{
                "id":"' .$GLOBALS['sid']. '"
            },
            "message":{
                "text":"Hi ' . $GLOBALS['username'] . ',\n
                Welcome to the myKaziBot app. I am Kazibot. \n
                I can help you find a job or find job applicants for your job. \n
                What would you like to do?",
                "quick_replies":[
                    {
                        "content_type":"text",
                        "title":"Find Job",
                        "payload":"userType_Find-Job"
                    },
                    {
                        "content_type":"text",
                        "title":"Post Job",
                        "payload":"userType_Post-Job"
                    },
                    {
                        "content_type":"text",
                        "title":"Find Place",
                        "payload":"find_place"
                    }
                ]
            }
        }';

    $GLOBALS['status_location'] = '
        {"recipient":{
            "id":"' . $GLOBALS['sid'] . '"
        },
        "message":{
            "text":"Please send me your prefered job location.\nEnter a city,country or send the location using the [send location] button below.",
            "quick_replies":[
                {"content_type":"location"}
            ]
        }
    }';
//"Hi ".$GLOBALS['username'].",\nI can help you find places around ".$myLoc.".\nJust type the command 'find [place]'.(e.g.find hospital,find police,find atm).")
$GLOBALS['find_location_place'] = '
    {"recipient":{
        "id":"' . $GLOBALS['sid'] . '"
    },
    "message":{
        "text":"Hi '.$GLOBALS['username'].',\nI can help you find places around you.\nBut first I need the location you wish to find the place from.",
        "quick_replies":[
            {
                "content_type":"text",
                "title":"Enter Location",
                "payload":"find_location"
            }
        ]
    }
}';

    $GLOBALS['find_location'] = '
        {"recipient":{
            "id":"' . $GLOBALS['sid'] . '"
        },
        "message":{
            "text":"Please send me the loction your wish to find places from.\nEither enter a city,country or send the location using the [send location] button below.",
            "quick_replies":[
                {"content_type":"location"}
            ]
        }
    }';

    $GLOBALS['status_job'] = '
    {"recipient":{
        "id":"' . $GLOBALS['sid'] . '"
    },
    "message":{
        "text":"What kind of job are you looking for (Just one Job)?\n eg. Part time, Accountant, Web Designer,Chef, Sales Person, Programmer, House Help)"
    }
}';

    $GLOBALS['status_experience'] = '
{"recipient":{
    "id":"' . $GLOBALS['sid'] . '"
},
"message":{
    "text":"How many years have you worked at this job?",
    "quick_replies":[
        {
            "content_type":"text",
            "title":"First Job",
            "payload":"experience_First-Job"
        },
        {
            "content_type":"text",
            "title":"Under 1 year",
            "payload":"experience_Under-1-Year"
        },
        {
            "content_type":"text",
            "title":"1 to 3 years",
            "payload":"experience_1-to-3-years"
        },
        {
            "content_type":"text",
            "title":"4 to 8 years",
            "payload":"experience_4-to-8-years"
        },
        {
            "content_type":"text",
            "title":"9 years and over",
            "payload":"expexperience_9-years-and-over"
        }
    ]
}
}';

    $GLOBALS['status_qualification'] = '
{"recipient":{
    "id":"' . $GLOBALS['sid'] . '"
},
"message":{
    "text":"What is your job qualification Level?",
    "quick_replies":[
        {
            "content_type":"text",
            "title":"Self Taught",
            "payload":"qualification_Self-Taught"
        },
        {
            "content_type":"text",
            "title":"Certificate",
            "payload":"qualification_Certificate"
        },
        {
            "content_type":"text",
            "title":"Collage Diploma",
            "payload":"qualification_Collage-Diploma"
        },
        {
            "content_type":"text",
            "title":"University Degree",
            "payload":"qualification_University-Degree"
        },
        {
            "content_type":"text",
            "title":"Masters Degree",
            "payload":"qualification_Masters-Degree"
        }
    ]
}
}';

    $GLOBALS['status_about'] = '
{"recipient":{
    "id":"' . $GLOBALS['sid'] . '"
},
"message":{
    "text":"Tell us a bit about yourself and the job you are looking for.\n
    eg.\n
    Hi,\n
    My is job. \n
    I am 1 year old and I and very passionate about helping people find jobs and employees.\n
    I like challenges and will raise to any challenge i meet or atleat try my hardest."
}
}';

    //payload with links and images
    $GLOBALS['status_search_job1'] = '{"recipient": {
    "id": "' . $GLOBALS['sid'] . '"
},
"message": {
    "attachment": {
        "type": "template",
        "payload": {
            "template_type": "generic",
            "elements": [{
                "title": "SuperJob Test Ltd.",
                "subtitle": "We have a job opening for a '. getField('job') .'",
                "buttons": [{
                    "type": "web_url",
                    "url": "https://www.oculus.com/en-us/rift/",
                    "title": "See Job 1"
                }, {
                    "type": "postback",
                    "title": "Search Again",
                    "payload": "search_job2"
                }, {
                    "type": "postback",
                    "title": "Edit Profile",
                    "payload": "edit_info"
                }]
            }]
        }
    }
}
}';
//✖ ✔️
$GLOBALS['status_delete'] = '
{"recipient":{
"id":"' . $GLOBALS['sid'] . '"
},
"message":{
"text":"Are you sure you want to delete your profile?",
"quick_replies":[
    {
        "content_type":"text",
        "title":"Yes ✔️",
        "payload":"delete_yes"
    },
    {
        "content_type":"text",
        "title":"No ✖",
        "payload":"delete_no"
    }
]
}
}';
$GLOBALS['status_search_job2'] = '{"recipient": {
"id": "' . $GLOBALS['sid'] . '"
},
"message": {
"attachment": {
    "type": "template",
    "payload": {
        "template_type": "generic",
        "elements": [{
            "title": "SuperJob Test Ltd.",
            "subtitle": "We have a job opening for a '. getField('job') .'",
            "buttons": [{
                "type": "web_url",
                "url": "https://www.oculus.com/en-us/rift/",
                "title": "See Job 2"
            }, {
                "type": "postback",
                "title": "Search Again",
                "payload": "search_job1"
            }, {
                "type": "postback",
                "title": "Edit Profile",
                "payload": "edit_info"
            }]
        }]
    }
}
}
}';

$GLOBALS['status_test'] = '{"recipient": {
"id": "' . $GLOBALS['sid'] . '"
},
"message": {
"attachment": {
    "type": "template",
    "payload": {
        "template_type": "generic",
        "elements": [{
            "title": "SuperJob Test Ltd.",
            "subtitle": "We have a job opening for a '. getField('job') .' ",
            "item_url": "https://www.oculus.com/en-us/rift/",
            "image_url": "http://messengerdemo.parseapp.com/img/rift.png",
            "buttons": [{
                "type": "web_url",
                "url": "https://www.oculus.com/en-us/rift/",
                "title": "Open Web URL"
            }, {
                "type": "postback",
                "title": "Callback",
                "payload": "PayloadTest"
            }]
        }]
    }
}
}
}';



// _________{COMPANY INFO}_________
/*
status_companyname
status_companydescription
status_companyjob
status_companyexperience
status_companyqualification
*/

$GLOBALS['status_companyname'] = '
{"recipient":{
    "id":"' . $GLOBALS['sid'] . '"
},
"message":{
    "text":"What is the name of the company or person posting the job."
}
}';

$GLOBALS['status_search_job'] = '{"recipient": {
"id": "' . $GLOBALS['sid'] . '"
},
"message": {
"attachment": {
    "type": "template",
    "payload": {
        "template_type": "generic",
        "elements": [{
            "title": "'.getField('companyname').'",
            "subtitle": "Job Description:- '.getField('companydescription').'\n Job:- '.getField('companyjob').'\n Location:- '.getField('companyLocation').'\n Experience:- '.getField('companyexperience').'\n Qualification:- '.getField('companyqualification').' ",
            "buttons": [
            {
                "type": "postback",
                "title": "Edit Name",
                "payload": "edit_companyname"
            },
            {
                "type": "postback",
                "title": "Edit Description",
                "payload": "edit_companydescription"
            },
            {
                "type": "postback",
                "title": "Edit Location",
                "payload": "edit_companylocation"
            }]
        }]
    }
}
}
}';
/*
,
{
    "type": "postback",
    "title": "Edit Experience",
    "payload": "edit_companyexperience"
},
{
    "type": "postback",
    "title": "Edit Qualification",
    "payload": "edit_companyqualification"
},
{
    "type": "postback",
    "title": "Exetend Time",
    "payload": "edit_companyjobtime"
},
{
    "type": "postback",
    "title": "Delete Job Posting",
    "payload": "edit_companydelete"
}
*/
$GLOBALS['status_companyinfo'] = '
{"recipient":{
    "id":"'.$GLOBALS['sid'].'"
},
"message":{
    "text":"Welcome '.getField('companyname').', \n
    This is the information you have entered. \n
    Job applicants matching your requirements will be notified of your job posting.\n
                                                        \n
    Job:- '.getField('companyjob').'\n
    Location:- '.getField('companyLocation').'\n
    Experience:- '.getField('companyexperience').'\n
    Qualification:- '.getField('companyqualification').'\n
    Phone:- '.getField('companyphone').'\n",
    "quick_replies":[
        {
            "content_type":"text",
            "title": "Edit Name",
            "payload": "edit_companyname"
        },
        {
            "content_type":"text",
            "title":"Find Place",
            "payload":"find_place"
        },
        {
            "content_type":"text",
            "title": "Edit Job",
            "payload": "edit_companyjob"
        },
        {
            "content_type":"text",
            "title": "Edit Location",
            "payload": "edit_companylocation"
        },
        {
            "content_type":"text",
            "title": "Edit Experience",
            "payload": "edit_companyexperience"
        },
        {
            "content_type":"text",
            "title": "Edit Qualification",
            "payload": "edit_companyqualification"
        },
        {
            "content_type":"text",
            "title": "Delete Job Posting",
            "payload": "delete_profile"
        }
    ]
}
}';

$GLOBALS['status_companylocation'] = '
    {"recipient":{
        "id":"' . $GLOBALS['sid'] . '"
    },
    "message":{
        "text":"Please enter your job location : (city,country) \n(Nairobi, Kenya) or use your current location from fbmessager.",
        "quick_replies":[
            {"content_type":"location"}
        ]
    }
}';

$GLOBALS['status_companydescription'] = '
{"recipient":{
    "id":"' . $GLOBALS['sid'] . '"
},
"message":{
    "text":"Enter a short description about the job."
}
}';

$GLOBALS['status_companyjob'] = '
{"recipient":{
    "id":"' . $GLOBALS['sid'] . '"
},
"message":{
    "text":"What is the job opening you are posting for? (e.g. Accountant, Web Designer, Chef, Sales)."
}
}';

$GLOBALS['status_companyexperience'] = '
{"recipient":{
"id":"' . $GLOBALS['sid'] . '"
},
"message":{
"text":"How much experience should job applicants have for this job?",
"quick_replies":[
    {
        "content_type":"text",
        "title":"None",
        "payload":"companyexperience_None"
    },
    {
        "content_type":"text",
        "title":"1 month and over",
        "payload":"companyexperience_1-month-and-over"
    },
    {
        "content_type":"text",
        "title":"1 year and over",
        "payload":"companyexperience_1-year-and-over"
    },
    {
        "content_type":"text",
        "title":"4 years and over",
        "payload":"companyexperience_4-years-and-over"
    },
    {
        "content_type":"text",
        "title":"9 years and over",
        "payload":"companyexpexperience_9-years-and-over"
    }
]
}
}';

$GLOBALS['status_companyqualification'] = '
{"recipient":{
"id":"' . $GLOBALS['sid'] . '"
},
"message":{
"text":"What is the minimum qualification Level needed for the job?",
"quick_replies":[
    {
        "content_type":"text",
        "title":"Self Taught",
        "payload":"companyqualification_Self-Taught"
    },
    {
        "content_type":"text",
        "title":"Certificate",
        "payload":"companyqualification_Certificate"
    },
    {
        "content_type":"text",
        "title":"Collage Diploma",
        "payload":"companyqualification_Collage-Diploma"
    },
    {
        "content_type":"text",
        "title":"University Degree",
        "payload":"companyqualification_University-Degree"
    },
    {
        "content_type":"text",
        "title":"Masters Degree",
        "payload":"companyqualification_Masters-Degree"
    }
]
}
}';
}


 ?>
