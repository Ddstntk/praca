import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {UsersComponent} from "../users/users.component";
import {ConfigComponent} from "../config/config.component";
import {LoginComponent} from "../login";
import { AuthGuard} from "../_guards";
import {LayoutComponent} from "../layout/layout.component";
import {LogoutComponent} from "../logout";
import {RegisterComponent} from "../register/register.component";
// import {LayoutComponent} from "./layout/layout.component";

const routes: Routes = [
  {
    path: '',
    pathMatch: 'full',
    redirectTo: 'dashboard'
  },
  { path: 'login', component: LoginComponent },
  { path: 'logout', component: LogoutComponent },
  {path: 'register', component: RegisterComponent},
  // {
  //   path: 'home',
  //   component: HomeComponent
  // },
  {
    path: 'dashboard',
    loadChildren: 'app/dashboard/dashboard.module#DashboardModule', canActivate: [AuthGuard]
  },
  {
    path: 'profile',
    redirectTo: 'profile', canActivate: [AuthGuard]
  },
  {
    path: 'profile',
    component: UsersComponent, canActivate: [AuthGuard]
  },
  // {
  //   path: 'user',
  //   redirectTo: 'user'
  // },
  {
    path: 'view/:id',
    component: UsersComponent, canActivate: [AuthGuard]
  },
  {
    path: 'config',
    redirectTo: 'config', canActivate: [AuthGuard]
  },
  {
    path: 'config',
    component: ConfigComponent, canActivate: [AuthGuard]
  },
  // {
  //   path: '**',
  //   redirectTo: 'home'
  // }
];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, {useHash: true})
  ],
  declarations: [],
  exports: [RouterModule]
})
export class RoutingModule {
}
