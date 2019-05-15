import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {DashboardComponent} from './dashboard/dashboard.component';
import {DashboardRoutingModule} from './routing/dashboard-routing.module';
import {DashboardCardsService} from './services/dashboard-cards/dashboard-cards.service';
import {DashboardChatComponent} from './cards/dashboard-chat/dashboard-chat.component';
import {DashboardCardsSpawnerComponent} from './cards/dashboard-cards-spawner/dashboard-cards-spawner.component';
import {
  MatButtonModule, MatCardModule, MatGridListModule, MatIconModule, MatListModule, MatSidenavModule,
  MatTooltipModule
} from '@angular/material';

import { FormsModule } from '@angular/forms';
import {FlexLayoutModule} from '@angular/flex-layout';
import {ChatComponentService} from "../chat/chat.component.service";
import {UsersComponentService} from "../users/users.component.service";

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
      FormsModule
  ],
  declarations: [DashboardComponent, DashboardChatComponent, DashboardCardsSpawnerComponent],
  providers: [DashboardCardsService, ChatComponentService, UsersComponentService]
})
export class DashboardModule {
}
