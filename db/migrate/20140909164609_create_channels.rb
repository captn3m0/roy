class CreateChannels < ActiveRecord::Migration
  def change
    create_table :channels do |t|
      t.string :name
      t.string :identifier

      t.timestamps null: false
    end
  end
end
