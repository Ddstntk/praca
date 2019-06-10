import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {DashboardComponent} from './dashboard/dashboard.component';
import {DashboardRoutingModule} from './routing/dashboard-routing.module';
import {DashboardCardsService} from './services/dashboard-cards/dashboard-cards.service';
import {DashboardChatComponent} from './cards/dashboard-chat/dashboard-chat.component';
import {DashboardPostsComponent} from "./cards/dashboard-posts/dashboard-posts.component";
import {DashboardCardsSpawnerComponent} from './cards/dashboard-cards-spawner/dashboard-cards-spawner.component';
import {
  MatButtonModule,
  MatCardModule,
  MatGridListModule,
  MatIconModule,
  MatListModule,
  MatProgressSpinnerModule,
  MatSidenavModule,
  MatTooltipModule
} from '@angular/material';

import { FormsModule } from '@angular/forms';
import {FlexLayoutModule} from '@angular/flex-layout';
import {ChatComponentService} from "../chat/chat.component.service";

import {UsersComponentService} from "./cards/dashboard-users/dashboard-users.component.service";
import {PostsComponentService} from "./cards/dashboard-posts/dashboard-posts.component.service";
import {DashboardFriendsComponent} from "./cards/dashboard-friends/dashboard-friends.component";
import {FriendsComponentService} from "./cards/dashboard-friends/dashboard-friends.component.service";
import {DashboardUsersComponent} from "./cards/dashboard-users/dashboard-users.component";
import {UsersFilterPipe} from "../_pipes/users.pipe";

@NgModule({
  imports: [
    CommonModule,
    MatGridListModule,
    MatButtonModule,
    MatCardModule,
    MatListModule,
    MatIconModule,
    MatTooltipModule,
    MatSidenavModule,
    FlexLayoutModule,
    DashboardRoutingModule,
      FormsModule,
    MatProgressSpinnerModule

  ],
  declarations: [
      DashboardComponent,
    DashboardChatComponent,
    DashboardPostsComponent,
    DashboardCardsSpawnerComponent,
    DashboardFriendsComponent,
      DashboardUsersComponent,
      UsersFilterPipe
  ],
  providers: [
      DashboardCardsService,
    ChatComponentService,
    // UsersComponentService,
    PostsComponentService,
    FriendsComponentService,
    UsersComponentService
  ]
})
export class DashboardModule {
}
