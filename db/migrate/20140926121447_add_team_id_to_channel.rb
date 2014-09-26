class AddTeamIdToChannel < ActiveRecord::Migration
  def change
    add_column :channels, :team_id, :integer
  end
end
