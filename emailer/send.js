
import { EmailClient } from "@azure/communication-email";
import * as process from 'process';

const argv = process.argv;
if(argv.length !== 5) {
    process.exit(1);
}

const to = argv[2];
const title = argv[3];
const body = argv[4];

const connectionString = `endpoint=https://proiect-comms.communication.azure.com/;accesskey=xb8gW0A8P600ct8qGYRduj6o1zvmNBuqbAPlksPIyBy2vTKdpRaWjxDE3bJyFz02kXc2Pya4k2ceRuZMH4FWxg==`;
const client = new EmailClient(connectionString);

const emailMessage = {
    sender: "DoNotReply@63afdd8c-a267-4cfa-a29a-325b4fb3a26d.azurecomm.net",
    content: {
        subject: title,
        plainText: body,
    },
    recipients: {
        to: [
            {
                email: to,
            },
        ],
    },
};

const response = await client.send(emailMessage);
