<!--
Title: Skype doesn't show notification for a specific contact
Description: Skype doesn't show notification for a specific contact
Date: 2013/05/15
Tags: troubles, skype
-->

If you **don't receive skype-notifications when one specific person writes you**,
that's mean it is Disabled :) I don't know how, but suddenly I found the same *bug* in my Skype.
A bug - because I didn't disable it.<!--cut-here-->

## How to fix it ?! ##
Yes, you're right - just enable it :D

Some guys ([here][1] and [here][2]) say:  
*Select that contact's name >>Click Conversation >> Notification Settings...>> Notify me*.

<img style="display: block; margin-left: auto; margin-right: auto;" title="Notification settings" alt="Select 'Notify me' in the Setting, or smth like that." src="http://community.skype.com/t5/image/serverpage/image-id/13209iC51702826146EC18" width="511" height="532" />

**Buuuut...** I haven't "Notification settings" at all :)
Instead I found [another solution][3] - you can enable notifications for any contact just by sending `/alertson` into chat with him.

Here some more useful commands from [official site][4]. For more information, please, look there.


    /add [Skype Name]	: Adds a contact to the chat.
    For instance: /add alex_cooper will add that member to the chat.

    /alertson [text]	: Allows you to specify what needs to appear in a chat for you to be notified.
    For example, /alertson London will only alert you when the word "London" appears in the chat.

    /alertsoff	: Disable message notifications.

    /get role	: Details your role in the chat.

    /get uri	: Creates a URL link that other people can use to join the group chat.

    /golive	: Starts a group call with other participants of the chat.

    /info	: Details number of people in chat and maximum number available.

    /me [text]	: Your name will appear followed by any text you write.
    For instance, "/me working from home" will cause the phrase "working from home" to appear next to your name in the chat. You can use this to send a message about your activities or status.

    /topic [text]	: Changes the chat topic.

    /undoedit	: Undo the last edit of your message.

    /whois [Skype Name]	: Provides details about a chat member such as current role.



[1]: http://community.skype.com/t5/Windows-desktop-client/Doesn-t-show-notification-from-only-one-person/td-p/693993 (Doesn't show notification from only one person)
[2]: http://community.skype.com/t5/Windows-desktop-client/Doesn-t-show-notification-for-a-specific-contact/td-p/1403352 (Doesn't show notification for a specific contact)
[3]: http://community.skype.com/t5/Skype-%D0%B4%D0%BB%D1%8F-Windows/%D0%BD%D0%B5%D1%82-%D0%B7%D0%B2%D1%83%D0%BA%D0%BE%D0%B2%D0%BE%D0%B3%D0%BE-%D0%BE%D0%BF%D0%BE%D0%B2%D0%B5%D1%89%D0%B5%D0%BD%D0%B8%D1%8F-%D0%BE%D1%82-%D0%BE%D0%B4%D0%BD%D0%BE%D0%B3%D0%BE-%D0%BA%D0%BE%D0%BD%D1%82%D0%B0%D0%BA%D1%82%D0%B0/td-p/462821 (Hет звукового оповещения от одного контакта)
[4]: https://support.skype.com/en/faq/FA10042/what-are-chat-commands-and-roles (What are chat commands and roles?)
