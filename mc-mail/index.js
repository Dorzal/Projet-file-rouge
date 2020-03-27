const express = require('express');
const router = express.Router();
const app = express();
app.use(express.json());
app.post('/api/mail', (req, res ,next) => {
    console.log(req.body);
    const mailjet = require ('node-mailjet')
        .connect('f54fe5a466e4dd47ed8a06db3dbcdfff', '3bdac2a2ad597fca3b041c2bb373c276')
    const request = mailjet
        .post("send", {'version': 'v3.1'})
        .request({
            "Messages":[
                {
                    "From": {
                        "Email": "arnaud.linder@viacesi.fr",
                        "Name": "Verretech"
                    },
                    "To": [
                        {
                            "Email": ""+req.body.Email+"",
                            "Name": ""+req.body.Firstname+""
                        }
                    ],
                    "Subject": "Votre commande n°"+req.body.Id+" vous attend",
                    "TextPart": "Votre commande n°"+req.body.Id+" vous attend",
                    "HTMLPart": "<h3>Merci pour votre commande !</h3><br /><p>la commande N°"+req.body.Id+ " est prête a être retirée</p>",
                    "CustomID": "AppGettingStartedTest"
                }
            ]
        })
    request
        .then((result) => {
            return res.sendStatus(200);
        })
        .catch((err) => {
            console.log(err.message);
            return res.sendStatus(500);
        })

} );


app.listen('80');



