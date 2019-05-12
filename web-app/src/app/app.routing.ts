import { Routes, RouterModule } from '@angular/router';

import { LoginComponent } from './login/index';
// import { HomeComponent } from './home/index';
import { AuthGuard } from './_guards/index';
import {LayoutComponent} from "./layout/layout.component";

const appRoutes: Routes = [
    { path: 'login', component: LoginComponent },
    { path: '', component: LayoutComponent, canActivate: [AuthGuard] },

    // otherwise redirect to home
    { path: '**', redirectTo: '' }
];

export const routing = RouterModule.forRoot(appRoutes);