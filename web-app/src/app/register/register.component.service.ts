import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { Observable } from 'rxjs';

@Injectable()
export class RegisterComponentService {

    constructor(
        private http: HttpClient) { }

    addUser(user): Observable<any> {
        return this.http.post('api/auth/signup', user);
    }

}
;