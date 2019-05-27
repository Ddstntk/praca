import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { Observable } from 'rxjs';

@Injectable()
export class PostsComponentService {

    constructor(
        private http: HttpClient) { }

    indexAction(page): Observable<any> {
        return this.http.get('api/posts/page/'+page);
    }

    allAction(): Observable<any> {
        return this.http.get('api/chat/all');
    }

    addAction(body): Observable<any> {
        return this.http.post('api/posts/add', body);
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
