import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms'; // <-- NgModel lives here
import {HTTP_INTERCEPTORS, HttpClient, HttpClientModule} from "@angular/common/http";

//LAYOUT
import { AppComponent } from './app.component';
// import { HeroesComponent } from './heroes/heroes.component';
import { LayoutComponent } from './layout/layout.component';
import { FriendsComponent } from './friends/friends.component';

//APP LOGIC
import { FriendsComponentService } from "./friends/friends.component.service";
import {UsersComponent} from "./users/users.component";
import {ChatComponent} from "./chat/chat.component";

//LOGIN
import {LoginComponent} from "./login/login.component";
import {AuthGuard} from "./_guards/auth.guard";
import {AuthenticationService} from "./_services/authentication.service";
import {UserService} from "./_services/user.service";
import {JwtInterceptor} from "./_helpers/jwt.interceptor";
import {fakeBackendProvider} from "./_helpers/fake-backend";
import {routing} from "./app.routing";
// import {HomeComponent} from "./home/home.component";
// import {DashboardComponent} from "./dashboard/dashboard/dashboard.component";
//WINDOWS
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {MatNativeDateModule, MatToolbarModule} from '@angular/material';
// import {DashboardComponent} from "./dashboard/dashboard/dashboard.component";
import {RoutingModule} from "./routing/routing.module";

// import { fakeBackendProvider } from './_helpers/index';
// import { AuthGuard } from './_guards/index';
// import { JwtInterceptor } from './_helpers/index';
// import { AuthenticationService, UserService } from './_services/index';
// import { LoginComponent } from './login/login.component';
// import { HomeComponent } from './home/index';

import {
  MatButtonModule, MatCardModule, MatGridListModule, MatIconModule, MatListModule, MatSidenavModule,
  MatTooltipModule
} from '@angular/material';
import {FlexLayoutModule} from "@angular/flex-layout";
import {NavbarComponent} from "./navbar/navbar.component";
import {ConfigComponent} from "./config/config.component";
import {ConfigComponentService} from "./config/config.component.service";
import {LogoutComponent} from "./logout";
import {RegisterComponent} from "./register/register.component";
import {RegisterComponentService} from "./register/register.component.service";



import {MatDatepickerModule} from '@angular/material/datepicker';


import {MatFormFieldModule} from '@angular/material/form-field';
@NgModule({
  declarations: [
    AppComponent,
      // HomeComponent,
    // HeroesComponent,
    LayoutComponent,
    FriendsComponent,
    UsersComponent,
      // DashboardComponent,
      ChatComponent,
      LoginComponent,
      LogoutComponent,
      RegisterComponent,
      NavbarComponent,
      ConfigComponent,
  ],
  imports: [
      MatFormFieldModule,
    MatNativeDateModule,
      MatDatepickerModule,
    MatGridListModule,
    MatButtonModule,
    MatCardModule,
    MatListModule,
    MatIconModule,
    MatTooltipModule,
    MatSidenavModule,
    FlexLayoutModule,
    BrowserModule,
    BrowserAnimationsModule,
    MatToolbarModule,
    RoutingModule,
    FormsModule,
      HttpClientModule,
    MatToolbarModule,
    BrowserModule,
    BrowserAnimationsModule,
      // routing
  ],
  providers: [
      FriendsComponentService,
      AuthGuard,
      AuthenticationService,
      UserService,
      ConfigComponentService,
      RegisterComponentService,

    {
      provide: HTTP_INTERCEPTORS,
      useClass: JwtInterceptor,
      multi: true
    },
      fakeBackendProvider
  ],
  bootstrap: [AppComponent]
})

export class AppModule { }
