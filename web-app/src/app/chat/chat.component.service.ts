import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { Observable } from 'rxjs';

@Injectable()
export class ChatComponentService {

    constructor(
        private http: HttpClient) { }

    indexAction(id): Observable<any> {
        return this.http.get('api/chat/view/'+id);
    }

    allAction(): Observable<any> {
        return this.http.get('api/chat/all');
    }

    sendAction(body): Observable<any> {
        return this.http.post('api/chat/send', body);
    }

    newAction(id): Observable<any> {
        return this.http.post('api/chat/new', id);
    }

    // passwordAction(body): Observable<any> {
    //     return this.http.post('api/user/password', body);
    // }
}


// $controller->get('/view', [$this, 'indexAction'])
// ->bind('chat_index_paginated');
// $controller->get('/all', [$this, 'indexChats'])
// ->bind('chat_index');
// $controller->match('/send', [$this, 'sendAction'])
// ->method('POST|GET')
// ->bind('messages_send');
// $controller->match('/new', [$this, 'newChat'])
// ->method('POST|GET')
// ->bind('conversation_new');
// $controller->match('/set/{id}', [$this, 'setChat'])
// ->method('POST|GET')
// ->bind('set_chat');
