import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import 'rxjs/add/operator/first';

import { AuthenticationService } from '../_services/authentication.service';

@Component({
    moduleId: module.id,
    templateUrl: 'logout.component.html'
})

export class LogoutComponent implements OnInit {
    model: any = {};
    loading = false;
    returnUrl: string;
    error = '';

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private authenticationService: AuthenticationService) { }

    ngOnInit() {
        // reset login status
        this.authenticationService.logout().subscribe((data)=>{
            console.log("Jestok")
        });

        // get return url from route parameters or default to '/'
        this.returnUrl = this.route.snapshot.queryParams['returnUrl'] || '/';
        this.logout();
    }

    logout() {
        this.loading = true;
        // this.authenticationService.logout()
        this.router.navigate(['login']);
    }
}