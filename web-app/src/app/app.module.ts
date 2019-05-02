import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms'; // <-- NgModel lives here
import {HttpClient, HttpClientModule} from "@angular/common/http";

import { AppComponent } from './app.component';
import { HeroesComponent } from './heroes/heroes.component';
import { LayoutComponent } from './layout/layout.component';
import { FriendsComponent } from './friends/friends.component';

import { FriendsComponentService } from "./friends/friends.component.service";
import {UsersComponent} from "./users/users.component";

@NgModule({
  declarations: [
    AppComponent,
    HeroesComponent,
    LayoutComponent,
    FriendsComponent,
    UsersComponent
  ],
  imports: [
    BrowserModule,
    FormsModule,
      HttpClientModule,
  ],
  providers: [
      FriendsComponentService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }