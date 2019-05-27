import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { Observable } from 'rxjs';

@Injectable()
export class UsersComponentService {

    constructor(
        private http: HttpClient) { }

    profileAction(): Observable<any> {
        return this.http.get('api/user/profile');
    }

    userAction(id): Observable<any> {
        return this.http.get('api/user/view/' + id);
    }
}

// $controller = $app['controllers_factory'];
// $controller->get('/profile', [$this, 'profileAction']);
// $controller->get('/view/{id}', [$this, 'viewAction']);
// $controller->get('/index', [$this, 'indexAction']);
// $controller->match('/edit', [$this, 'editAction'])
// ->method('GET|POST');
// $controller->match('/password', [$this, 'changePassword'])
// ->method('GET|POST');